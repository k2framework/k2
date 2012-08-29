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
     *  @var EventDispatcher
     */
    protected $dispatcher;

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
        $this->namespaces = $this->registerNamespaces();

        if ($this->production) {
            error_reporting(0);
            ini_set('display_errors', 'Off');
        } else {
            error_reporting(-1);
            ini_set('display_errors', 'On');
            ExceptionHandler::handle();
        }

        //creamos la instancia del AppContext
        $context = new AppContext($this->request, $this->getAppPath(), $this->namespaces);

        $config = new ConfigContainer($context);
        $config->compile();

        $this->initContainer($config->getConfig());

        $this->container->set('app.context', $context);

        $this->initDispatcher($config->getConfig());
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

        //ejecutamos el evento request
        $this->dispatcher->dispatch(KumbiaEvents::REQUEST, new RequestEvent($request));

            $resolver = new ControllerResolver($this->container);

        //obtenemos la instancia del controlador, el nombre de la accion
        //a ejecutar, y los parametros que recibirá dicha acción
        list($controller, $action, $params) = $resolver->getController($request);

        //ejecutamos el evento controller.
        $this->dispatcher
               ->dispatch(KumbiaEvents::CONTROLLER, new ControllerEvent($request, array($controller, $action)));
        //ejecutamos la acción de controlador pasandole los parametros.
        $response = $resolver->executeAction($action, $params);


        if (!$response instanceof Response) {

            //si no es una instancia de KumbiaPHP\Kernel\Controller\Controller
            //lanzamos una excepción
            if (!$controller instanceof \KumbiaPHP\Kernel\Controller\Controller) {
                throw new \LogicException(sprintf("El controlador debe retornar una Respuesta"));
            } else {
                //como la acción no devolvió respuesta, debemos
                //obtener la vista y el template establecidos en el controlador
                //para pasarlos al servicio view, y este construya la respuesta
                $view = $resolver->getView($action);
                $template = $resolver->getTemplate();
                $properties = $resolver->getPublicProperties(); //nos devuelve las propiedades publicas del controlador
                //llamamos al render del servicio "view" y esté nos devolverá
                //una instancia de response con la respuesta creada
                $response = $this->container->get('view')->render($template, $view, $properties);
            }
        }

        //ejecutamos el evento response.
        $this->dispatcher->dispatch(KumbiaEvents::RESPONSE, new ResponseEvent($request, $response));
        //retornamos la respuesta
        return $response;
    }

    abstract protected function registerNamespaces();

    private function getAppPath()
    {
        $r = new \ReflectionObject($this);
        return dirname($r->getFileName()) . '/';
    }

    /**
     * Esta función inicializa el contenedor de servicios.
     */
    protected function initContainer(Parameters $config)
    {


        $definitions = new DefinitionManager();

        foreach ($config->get('services')->all() as $id => $class) {
            $definitions->addService(new \KumbiaPHP\Di\Definition\Service($id, $class));
        }

        foreach ($config->get('parameters')->all() as $id => $value) {
            $definitions->addParam(new \KumbiaPHP\Di\Definition\Parameter($id, $value));
        }

        $this->di = new DependencyInjection();

        $this->container = new Container($this->di, $definitions);
    }

    protected function initDispatcher(Parameters $config)
    {
        $this->dispatcher = new EventDispatcher($this->container);
        foreach ($config->get('listeners')->all() as $service => $params) {
            if ($config->get('services')->has($service)) {
                foreach ($params as $method => $event) {
                    $this->dispatcher->addListenerr($event, array($service, $method));
                }
            } else {
                throw new \LogicException("Se ha definido el escucha <b>$service</b> pero este no es un Servicio");
            }
        }
    }

}