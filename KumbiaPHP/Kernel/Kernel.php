<?php

namespace KumbiaPHP\Kernel;

use KumbiaPHP\Loader\Autoload;
use KumbiaPHP\Kernel\KernelInterface;
use KumbiaPHP\Kernel\Event\RequestEvent;
use KumbiaPHP\Kernel\AppContext;
use KumbiaPHP\Kernel\Controller\ControllerResolver;
use KumbiaPHP\Kernel\Event\KumbiaEvents;
use KumbiaPHP\EventDispatcher\EventDispatcher;
use KumbiaPHP\Kernel\Event\ControllerEvent;
use KumbiaPHP\Kernel\Event\ResponseEvent;
use KumbiaPHP\Kernel\Config\ConfigContainer;
use KumbiaPHP\Di\DependencyInjection;
use KumbiaPHP\Di\Container\Container;
use KumbiaPHP\Di\Definition\DefinitionManager;
use KumbiaPHP\Kernel\Exception\ExceptionHandler;

require_once __DIR__ . '/../Loader/Autoload.php';
require_once __DIR__ . '/KernelInterface.php';

Autoload::register();

/**
 * Description of Kernel
 *
 * @author manuel
 */
abstract class Kernel implements KernelInterface
{

    protected $modules;
    protected $namespaces;

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

    /**
     *
     * @var boolean 
     */
    protected $production;

    public function __construct($production = FALSE)
    {
        $this->production = $production;

        Autoload::registerDirectories(
                $this->registerNamespaces()
        );
    }

    protected function init()
    {
        $this->modules = $this->registerModules();
        $this->namespaces = $this->registerNamespaces();

        if ($this->production) {
            error_reporting(0);
            ini_set('display_errors', 'Off');
        } else {
            error_reporting(-1);
            ini_set('display_errors', 'On');
            ExceptionHandler::handle();
        }

        $this->getDefaultModule();

        $this->initContainer();
    }

    //put your code here
    public function execute(Request $request)
    {
        $this->request = $request;

        $this->init();

        //le asignamos el servicio session al request
        $request->setSession($this->container->get('session'));
        //agregamos el request al container
        $this->container->set('request', $request);

        $dispatcher = new EventDispatcher();

        //ejecutamos el evento request
        $dispatcher->dispatch(KumbiaEvents::REQUEST, new RequestEvent($request));

        $resolver = new ControllerResolver($this->container);

        list($controller, $action, $params) = $resolver->getController($request);

        //ejecutamos el evento controller.
        $dispatcher
                ->dispatch(KumbiaEvents::CONTROLLER, new ControllerEvent($request, array($controller, $action)));

        $response = call_user_func_array(array($controller, $action), $params);


        if (!$response instanceof Response) {

            //si la acción no devolvió respuesta,
            $view = $resolver->getView();
            $template = $resolver->getTemplate();
            $properties = $resolver->getPublicProperties();
            $response = $this->container->get('view')->render($template, $view, $properties);
        }

        //ejecutamos el evento response.
        $dispatcher->dispatch(KumbiaEvents::RESPONSE, new ResponseEvent($request, $response));

        return $response;
    }

    abstract protected function registerModules();

    abstract protected function registerNamespaces();

    protected function getModules()
    {
        return $this->modules;
    }

    public function getModulesAutoload()
    {
        $modules = array();
        $mods = array_merge($this->registerModules(), $this->namespaces);
        foreach ($mods as $module => $path) {
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
    protected function getDefaultModule()
    {
        return key($this->modules);
    }

    private function getAppPath()
    {
        $r = new \ReflectionObject($this);
        return dirname($r->getFileName()) . '/';
    }

    /**
     * Esta función inicializa el contenedor de servicios.
     */
    protected function initContainer()
    {
        //creamos la instancia del AppContext
        $context = new AppContext($this->request, $this->getAppPath(), $this->namespaces);

        $config = new ConfigContainer($context);
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
        $this->container->set('app.context', $context);
    }

}