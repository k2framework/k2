<?php

namespace KumbiaPHP\Kernel;

use KumbiaPHP\Kernel\KernelInterface;
use KumbiaPHP\Kernel\Controller\ControllerResolver;
use KumbiaPHP\Kernel\Event\KumbiaEvents;
use KumbiaPHP\EventDispatcher\EventDispatcher;
use KumbiaPHP\Kernel\Event\RequestEvent;
use KumbiaPHP\Kernel\Event\ControllerEvent;
use KumbiaPHP\Kernel\Event\ResponseEvent;
use KumbiaPHP\Kernel\Config\ConfigContainer;
use KumbiaPHP\Di\DependencyInjection;
use KumbiaPHP\Di\Container\Container;
use KumbiaPHP\Di\Definition\DefinitionManager;
use KumbiaPHP\Kernel\Exception\ExceptionHandler;

/**
 * Description of Kernel
 *
 * @author manuel
 */
abstract class Kernel implements KernelInterface {

    protected $modules;
    protected $defaultModule;
    protected $defaultController = 'Index';
    protected $defaultAction = 'index';

    /**
     *
     * @var DependencyInjection
     */
    protected $di;

    /**
     *  
     * @var Container
     */
    protected $container;

    /**
     *
     * @var Request 
     */
    protected $request;
    private $appPath;
    protected $production;

    public function __construct($production = FALSE) {
        \KumbiaPHP\Loader\Autoload::registerDirectories(
                $this->getModulesAutoload()
        );
        if ($this->production = $production) {
            error_reporting(0);
            ini_set('display_errors', 'Off');
        } else {
            error_reporting(-1);
            ini_set('display_errors', 'On');
            ExceptionHandler::handle();
        }
    }

    protected function init() {
        $this->modules = $this->registerModules();

        $this->getDefaultModule();

        //fix para que carge la config de core
        array_unshift($this->modules, dirname(__DIR__));

        $this->initContainer();
    }

    //put your code here
    public function execute(Request $request) {
        $this->request = $request;

        $this->init();

        //le asignamos el servicio session al request
        $request->setSession($this->container->get('session'));
        //agregamos el request al container
        $this->container->set('request', $request);

        $dispatcher = new EventDispatcher();

        //ejecutamos el evento request
        $dispatcher->dispatch(KumbiaEvents::REQUEST, new RequestEvent($request));

        $resolver = new ControllerResolver($this);

        list($controller, $action, $params) = $resolver->getController($request);

        //ejecutamos el evento controller.
        $dispatcher->dispatch(KumbiaEvents::CONTROLLER, new ControllerEvent($request, array($controller, $action)));

        $response = call_user_func_array(array($controller, $action), $params);


        if (!$response instanceof Response) {

            //si la acción no devolvió respuesta,
            $view = $resolver->getView();
            $template = $resolver->getTemplate();
            $properties = $resolver->getPublicProperties();
            $response = $this->container->get('template')->render($template, $view, $properties);
        }

        //ejecutamos el evento response.
        $dispatcher->dispatch(KumbiaEvents::RESPONSE, new ResponseEvent($request, $response));

        return $response;
    }

    abstract protected function registerModules();

    public function getContainer() {
        return $this->container;
    }

    public function getModules() {
        return $this->modules;
    }

    public function getModulesAutoload() {
        $modules = array();
        foreach ($this->registerModules() as $module => $path) {
            $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
            $path = explode('/', $path);
            array_pop($path); //quito el ultimo elemento que es la carpeta del modulo
            $modules[$module] = join('/', $path);
        }
        return $modules;
    }

    /**
     * Devuleve el nombre modulo a cargar por defecto si no 
     * se especifica nada en la URL.
     * 
     * Ese modulo será el que esté de primero en la lista de modulos cargados.
     * 
     * @return string 
     */
    public function getDefaultModule() {
        if ($this->defaultModule) {
            return $this->defaultModule;
        }

        return $this->defaultModule = key($this->modules);
    }

    public function getDefaultController() {
        return $this->defaultController;
    }

    public function getDefaultAction() {
        return $this->defaultAction;
    }

    /**
     *
     * @return Request 
     */
    public function getRequest() {
        return $this->request;
    }

    public function getAppPath() {
        if (!$this->appPath) {
            $this->appPath = $this->createAppPath();
        }
        return $this->appPath;
    }

    private function createAppPath() {
        $r = new \ReflectionObject($this);
        return dirname($r->getFileName()) . '/';
    }

    /**
     * Esta función inicializa el contenedor de servicios.
     */
    protected function initContainer() {
        $config = new ConfigContainer($this);
        $config->compile();

        $definitions = new DefinitionManager();

        foreach ($config->getConfig()->get('services')->all() as $id => $class) {
            $definitions->addService(new \KumbiaPHP\Di\Definition\Service($id, $class));
        }
//        foreach ($config->getConfig()->get('parameters')->all() as $id => $class) {
//            $definitions->addParam(new \KumbiaPHP\Di\Definition\Parameter($id, $class));
//        }

        $this->di = new DependencyInjection();

        $this->container = new Container($this->di, $definitions);
    }

}