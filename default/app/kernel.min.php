<?php

namespace KumbiaPHP\Kernel\Event;

use KumbiaPHP\Kernel\Event\RequestEvent;
use KumbiaPHP\Kernel\Request;
use KumbiaPHP\Kernel\Response;


class ResponseEvent extends RequestEvent
{

    protected $response;

    function __construct(Request $request, Response $response)
    {
        $this->response = $response;
    }

    
    public function getResponse()
    {
        return $this->response;
    }

}



namespace KumbiaPHP\Kernel;

use KumbiaPHP\Kernel\Collection;


class Response implements \Serializable
{

    
    public $headers;

    
    protected $content;

    
    protected $statusCode;

    
    protected $charset;

    
    protected $cache;

    
    public function __construct($content = NULL, $statusCode = 200, array $headers = array())
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = new Collection($headers);
        $this->cache = array();
    }

    
    public function getContent()
    {
        return $this->content;
    }

    
    public function setContent($content)
    {
        $this->content = $content;
    }

    
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    
    public function getCharset()
    {
        return $this->charset;
    }

    
    public function setCharset($charset)
    {
        $this->charset = $charset ? : 'UTF-8';
    }

    
    public function send()
    {
        $this->sendHeaders();
        $this->sendContent();
    }

    
    protected function sendHeaders()
    {
        if (headers_sent()) {
            return;
        }

        if (!$this->headers->has('Content-Type')) {
            $charset = $this->getCharset() ? : 'UTF-8';
            $this->headers->set('Content-Type', "text/html; charset=$charset");
        }

        //mandamos el status
        header(sprintf('HTTP/1.0 %s', $this->statusCode));

        foreach ($this->headers->all() as $index => $value) {
            if (is_string($index)) {
                header("{$index}: {$value}", false);
            } else {
                header("{$value}", false);
            }
        }
    }

    public function serialize()
    {
        return serialize(array(
                    'headers' => $this->headers->all(),
                    'content' => $this->getContent(),
                    'statusCode' => $this->getStatusCode(),
                    'charset' => $this->getCharset(),
                ));
    }

    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        $this->headers = new Collection($data['headers']);
        $this->setContent($data['content']);
        $this->setStatusCode($data['statusCode']);
        $this->setCharset($data['charset']);
    }

    public function cache($lifetime = NULL, $group = 'default')
    {
        if (NULL !== $lifetime) {
            $this->headers->set('cache-control', 'public');
            $lastModified = new \DateTime();
            $lastModified->setTimezone(new \DateTimeZone('UTC'));
            $this->headers->set('last-modified', $lastModified->format('D, d M Y H:i:s') . ' GMT');
            $expires = $lastModified->modify($lifetime);
            $this->headers->set('expires', $expires->format('D, d M Y H:i:s') . ' GMT');
            $this->cache = array(
                'time' => $lifetime,
                'group' => $group,
            );
        } else {
            $this->headers->delete('expires');
            $this->cache = array();
        }
    }

    public function getCacheInfo()
    {
        return $this->cache;
    }

    public function setNotModified()
    {
//        $this->setStatusCode(304);
//        $this->setContent(NULL);
    }

    
    protected function sendContent()
    {
        echo $this->content;
        while (ob_get_level()) {
            ob_end_flush(); //vamos limpiando y mostrando todos los niveles de buffer creados.
        }
    }

}



namespace KumbiaPHP\Kernel\Event;

use KumbiaPHP\Kernel\Request;
use KumbiaPHP\Kernel\Event\RequestEvent;


class ControllerEvent extends RequestEvent
{

    protected $controller = array();

    function __construct(Request $request, array $controller = array())
    {
        parent::__construct($request);
        $this->controller = $controller;
    }

    public function getController()
    {
        return $this->controller[0];
    }

    public function setController($controller)
    {
        $this->controller[0] = $controller;
    }

    public function getAction()
    {
        return $this->controller[1];
    }

    public function setAction($action)
    {
        $this->controller[1] = $action;
    }

    public function getParameters()
    {
        return $this->controller[2];
    }

    public function setParameters(array $parameters)
    {
        $this->controller[2] = $parameters;
    }

}



namespace KumbiaPHP\Kernel\Controller;

use KumbiaPHP\Di\Container\ContainerInterface;
use KumbiaPHP\Kernel\Request;
use KumbiaPHP\Kernel\Router\Router;
use KumbiaPHP\Kernel\Response;


class Controller
{

    
    protected $container;

    
    protected $view;

    
    protected $template = 'default';

    
    protected $cache = NULL;

    
    protected $limitParams = TRUE;

    
    protected $parameters;

    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    protected function renderNotFound($message)
    {
        throw new \KumbiaPHP\Kernel\Exception\NotFoundException($message);
    }

    
    protected function get($id)
    {
        return $this->container->get($id);
    }

    
    protected function getRequest()
    {
        return $this->container->get('request');
    }

    
    protected function getRouter()
    {
        return $this->container->get('router');
    }

    
    protected function setView($view, $template = FALSE)
    {
        $this->view = $view;
        if ($template !== FALSE) {
            $this->setTemplate($template);
        }
    }

    
    protected function setTemplate($template)
    {
        $this->template = $template;
    }

    
    protected function getView()
    {
        return $this->view;
    }

    
    protected function getTemplate()
    {
        return $this->template;
    }

    
    protected function cache($time = FALSE)
    {
        $this->cache = $time;
    }

    
    protected function render(array $params = array(), $time = NULL)
    {
        return $this->get('view')->render($this->template, $this->view, $params, $time);
    }

}



namespace KumbiaPHP\Kernel\Controller;

use KumbiaPHP\Di\Container\ContainerInterface;
use KumbiaPHP\Kernel\Exception\NotFoundException;
use \ReflectionClass;
use \ReflectionObject;
use KumbiaPHP\Kernel\Event\ControllerEvent;


class ControllerResolver
{

    
    protected $container;
    protected $module;
    protected $controller;
    protected $contShortName;
    protected $action;

    public function __construct(ContainerInterface $con)
    {
        $this->container = $con;
    }

    public function getController()
    {
        $controller = 'Index'; //controlador por defecto si no se especifica.
        $action = 'index'; //accion por defecto si no se especifica.
        $params = array(); //parametros de la url, de existir.
        //obtenemos la url actual de la petición.
        $currentUrl = '/' . trim($this->container->get('app.context')->getCurrentUrl(), '/');

        if (!$module = $this->getModule($currentUrl)) {
            throw new NotFoundException(sprintf("La ruta \"%s\" no concuerda con ningún módulo ni controlador en la App", $currentUrl), 404);
        }

        if ($url = explode('/', trim(substr($currentUrl, strlen($module)), '/'))) {

            //ahora obtengo el controlador
            if (current($url)) {
                //si no es un controlador lanzo la excepcion
                if (!$this->isController($module, current($url))) {
                    $controller = $this->camelcase(current($url));
                    if ('/' !== $module) {
                        throw new NotFoundException(sprintf("El controlador \"%sController\" para el Módulo \"%s\" no Existe", $controller, $module), 404);
                    } else {
                        throw new NotFoundException(sprintf("La ruta \"%s\" no concuerda con ningún módulo ni controlador en la App", $currentUrl), 404);
                    }
                }
                $controller = $this->camelcase(current($url));
                next($url);
            }
            //luego obtenemos la acción
            if (current($url)) {
                $action = $this->camelcase(current($url), TRUE);
                next($url);
            }
            //por ultimo los parametros
            if (current($url)) {
                $params = array_slice($url, key($url));
            }
        }

        $this->module = $module;
        $this->contShortName = $controller;
        $this->action = $action;

        $app = $this->container->get('app.context');
        $app->setCurrentModule($module);
        $app->setCurrentController($controller);

        return $this->createController($params);
    }

    
    protected function camelcase($string, $firstLower = FALSE)
    {
        $string = str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower($string))));
        if ($firstLower) {
            // Notacion lowerCamelCase
            $string[0] = strtolower($string[0]);
            return $string;
        } else {
            return $string;
        }
    }

    protected function getModule($url)
    {
        $routes = array_keys($this->container->get('app.context')->getModules());

        usort($routes, function($a, $b) {
                    return (strlen($a) > strlen($b)) ? -1 : 1;
                }
        );

        foreach ($routes as $route) {
            if (0 === strpos($url, $route)) {
                if ('/' === $route) {
                    return $route;
                } elseif ('/' === substr($url, strlen($route), 1) || strlen($url) === strlen($route)) {
                    return $route;
                }
            }
        }
        return FALSE;
    }

    protected function isController($module, $controller)
    {
        $path = $this->container->get('app.context')->getModules($module);
        return is_file("{$path}/Controller/{$this->camelcase($controller)}Controller.php");
    }

    protected function createController($params)
    {
        //creo el namespace para poder crear la instancia del controlador
        $currentPath = $this->container->get('app.context')->getModules($this->module);
        $modulesPath = $this->container->get('app.context')->getModulesPath();
        $namespace = substr($currentPath, strlen($modulesPath));
        //creo el nombre del controlador con el sufijo Controller
        $controllerName = $this->contShortName . 'Controller';
        //uno el namespace y el nombre del controlador.
        $controllerClass = str_replace('/', '\\', $namespace . 'Controller/') . $controllerName;

        try {
            $reflectionClass = new ReflectionClass($controllerClass);
        } catch (\Exception $e) {
            throw new NotFoundException(sprintf("No exite el controlador \"%s\" en el Módulo \"%sController/\"", $controllerName, $currentPath), 404);
        }

        $this->controller = $reflectionClass->newInstanceArgs(array($this->container));
        $this->setViewDefault($this->action);

        return array($this->controller, $this->action, $params);
    }

    public function executeAction(ControllerEvent $controllerEvent)
    {
        $this->controller = $controllerEvent->getController();
        $this->action = $controllerEvent->getAction();

        $controller = new ReflectionObject($this->controller);

        $this->executeBeforeFilter($controller);

        if (FALSE === $this->action) {
            return NULL; //si el before devuelve false, es porque no queremos que se ejecute nuestra acción.
        }
        $this->validateAction($controller, $controllerEvent->getParameters());

        $response = call_user_func_array(array($this->controller, $this->action), $controllerEvent->getParameters());

        $this->executeAfterFilter($controller);

        return $response;
    }

    public function getPublicProperties()
    {
        return get_object_vars($this->controller);
    }

    public function getModulePath()
    {
        $namespaces = $this->container->get('app.context')->getModules();
        return rtrim($namespaces[$this->module] . '/') . '/' . $this->module;
    }

    protected function validateAction(\ReflectionObject $controller, array $params)
    {
        if ($controller->hasProperty('limitParams')) {
            $limitParams = $controller->getProperty('limitParams');
            $limitParams->setAccessible(true);
            $limitParams = $limitParams->getValue($this->controller);
        } else {
            $limitParams = TRUE; //por defeto siempre limita los parametro
        }

        if ($controller->hasProperty('parameters')) {
            $parameters = $controller->getProperty('parameters');
            $parameters->setAccessible(true);
            $parameters->setValue($this->controller, $params);
        }
        //verificamos la existencia del metodo.
        if (!$controller->hasMethod($this->action)) {
            throw new NotFoundException(sprintf("No exite el metodo \"%s\" en el controlador \"%sController\"", $this->action, $this->contShortName), 404);
        }

        $reflectionMethod = $controller->getMethod($this->action);

        //verificamos que no sea el constructor a quien se llama
        if ($reflectionMethod->isConstructor()) {
            throw new NotFoundException(sprintf("Se está intentando ejecutar el constructor del controlador como una acción, en el controlador \"%sController\"", $this->contShortName), 404);
        }

        if (in_array($this->action, array('beforeFilter', 'afterFilter'))) {
            throw new NotFoundException(sprintf("Se está intentando ejecutar el filtro \"%s\" del controlador \"%sController\"", $this->action, $this->contShortName), 404);
        }

        //el nombre del metodo debe ser exactamente igual al camelCase
        //de la porcion de url
        if ($reflectionMethod->getName() !== $this->action) {
            throw new NotFoundException(sprintf("No exite el metodo <b>%s</b> en el controlador \"%sController\"", $this->action, $this->contShortName), 404);
        }

        //se verifica que el metodo sea public
        if (!$reflectionMethod->isPublic()) {
            throw new NotFoundException(sprintf("Éstas Tratando de acceder a un metodo no publico \"%s\" en el controlador \"%sController\"", $this->action, $this->contShortName), 404);
        }

        
        if ($limitParams && (count($params) < $reflectionMethod->getNumberOfRequiredParameters() ||
                count($params) > $reflectionMethod->getNumberOfParameters())) {

            throw new NotFoundException(sprintf("Número de parámetros erróneo para ejecutar la acción \"%s\" en el controlador \"%sController\"", $this->action, $this->contShortName), 404);
        }
    }

    protected function executeBeforeFilter(ReflectionObject $controller)
    {
        if ($controller->hasMethod('beforeFilter')) {
            $method = $controller->getMethod('beforeFilter');
            $method->setAccessible(TRUE);

            if (NULL !== $result = $method->invoke($this->controller)) {
                if (FALSE === $result) {
                    //si el resultado es false, es porque no queremos que se ejecute la acción
                    $this->action = FALSE;
                    $this->container->get('app.context')->setCurrentAction(FALSE);
                    return;
                }
                if (!is_string($result)) {
                    throw new NotFoundException(sprintf("El método \"beforeFilter\" solo puede devolver una cadena, en el Controlador \"%sController\"", $this->contShortName));
                }
                if (!$controller->hasMethod($result)) {
                    throw new NotFoundException(sprintf("El método \"beforeFilter\" está devolviendo el nombre de una acción inexistente \"%s\" en el Controlador \"%sController\"", $result, $this->contShortName));
                }
                //si el beforeFilter del controlador devuelve un valor, el mismo será
                //usado como el nuevo nombre de la acción a ejecutar.
                $this->action = $result;
                $this->container->get('app.context')->setCurrentAction($result);
            }
        }
    }

    protected function executeAfterFilter(ReflectionObject $controller)
    {
        if ($controller->hasMethod('afterFilter')) {
            $method = $controller->getMethod('afterFilter');
            $method->setAccessible(TRUE);
            $method->invoke($this->controller);
        }
    }

    public function getParamValue($propertie)
    {
        $reflection = new \ReflectionClass($this->controller);

        if ($reflection->hasProperty($propertie)) {

            //obtengo el parametro del controlador.
            $propertie = $reflection->getProperty($propertie);

            //lo hago accesible para poderlo leer
            $propertie->setAccessible(true);

            //y retorno su valor
            return $propertie->getValue($this->controller);
        } else {
            return NULL;
        }
    }

    protected function setViewDefault($action)
    {
        $reflection = new \ReflectionClass($this->controller);

        //obtengo el parametro del controlador.
        $propertie = $reflection->getProperty('view');

        //lo hago accesible para poderlo leer
        $propertie->setAccessible(true);
        $propertie->setValue($this->controller, $action);
    }

}



namespace KumbiaPHP\EventDispatcher;


class Event
{

    
    protected $propagationStopped = FALSE;

    
    public function stopPropagation()
    {
        $this->propagationStopped = TRUE;
    }

    
    public function isPropagationStopped()
    {
        return $this->propagationStopped;
    }

}



namespace KumbiaPHP\Kernel\Event;

use KumbiaPHP\Kernel\Request;
use KumbiaPHP\Kernel\Response;
use KumbiaPHP\EventDispatcher\Event;


class RequestEvent extends Event
{

    
    protected $request;

    
    protected $response;

    function __construct(Request $request)
    {
        $this->request = $request;
    }

    
    public function getRequest()
    {
        return $this->request;
    }

    
    public function hasResponse()
    {
        return $this->response instanceof Response;
    }

    
    public function getResponse()
    {
        return $this->response;
    }

    
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

}



namespace KumbiaPHP\Kernel\Event;


final class KumbiaEvents
{

    const REQUEST = 'kumbia.request';
    const CONTROLLER = 'kumbia.controller';
    const RESPONSE = 'kumbia.response';
    const EXCEPTION = 'kumbia.exception';

}



namespace KumbiaPHP\EventDispatcher;

use KumbiaPHP\EventDispatcher\Event;


interface EventDispatcherInterface
{

    
    public function dispatch($eventName, Event $event);

    
    public function addListener($eventName, $listener);

    
    public function hasListener($eventName, $listener);

    
    public function removeListener($eventName, $listener);
}



namespace KumbiaPHP\EventDispatcher;

use KumbiaPHP\EventDispatcher\EventDispatcherInterface;
use KumbiaPHP\Di\Container\ContainerInterface;


class EventDispatcher implements EventDispatcherInterface
{

    
    protected $listeners = array();

    
    protected $container;

    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    
    public function dispatch($eventName, Event $event)
    {
        if (!array_key_exists($eventName, $this->listeners)) {
            return;
        }
        if (is_array($this->listeners[$eventName]) && count($this->listeners[$eventName])) {
            foreach ($this->listeners[$eventName] as $listener) {
                $service = $this->container->get($listener[0]);
                call_user_func(array($service, $listener[1]), $event);
                if ($event->isPropagationStopped()) {
                    return;
                }
            }
        }
    }

    
    public function addListener($eventName, $listener)
    {
        if (!$this->hasListener($eventName, $listener)) {
            $this->listeners[$eventName][] = $listener;
        }
    }

    
    public function hasListener($eventName, $listener)
    {
        if (isset($this->listeners[$eventName])) {
            return in_array($listener, $this->listeners[$eventName]);
        } else {
            return FALSE;
        }
    }

    
    public function removeListener($eventName, $listener)
    {
        if ($this->hasListener($eventName, $listener)) {
            do {
                if ($listener === current($this->listeners[$eventName])) {
                    $key = key(current($this->listeners[$eventName]));
                    break;
                }
            } while (next($this->listeners[$eventName]));
        }
        unset($this->listeners[$eventName][$key]);
    }

}



namespace KumbiaPHP\Di\Container;


class Services
{

    
    protected $services;

    
    function __construct(array $services = array())
    {
        $this->services = $services;
    }

    
    public function add($id, $service)
    {
        if (!$this->has($id)) {
            $this->services[$id] = $service;
        }
    }

    
    public function has($id)
    {
        return isset($this->services[$id]);
    }

    
    public function get($id)
    {
        return $this->has($id) ? $this->services[$id] : NULL;
    }

    
    public function remove($id)
    {
        if (!$this->has($id)) {
            unset($this->services[$id]);
        }
    }

    
    public function replace($id, $service)
    {
        $this->services[$id] = $service;
    }

    
    public function clear()
    {
        $this->services = array();
    }

}



namespace KumbiaPHP\Di\Container;


interface ContainerInterface
{

    
    public function get($id);

    
    public function has($id);

    
    public function getParameter($id);

    
    public function hasParameter($id);
}




namespace KumbiaPHP\Di\Container;

use KumbiaPHP\Di\Container\ContainerInterface;
use KumbiaPHP\Di\DependencyInjectionInterface as Di;
use KumbiaPHP\Di\Definition\DefinitionManager;
use KumbiaPHP\Di\Container\Services;
use KumbiaPHP\Di\Definition\Service;
use KumbiaPHP\Di\Exception\IndexNotDefinedException;


class Container implements ContainerInterface
{

    
    protected $services;

    
    protected $di;

    
    protected $definitioManager;

    public function __construct(Di $di, DefinitionManager $dm = NULL)
    {
        $this->services = new Services();
        $this->di = $di;
        $this->definitioManager = $dm ? : new DefinitionManager();

        $di->setContainer($this);

        //agregamos al container como servicio.
        $this->set('container', $this);
    }

    public function get($id)
    {

        if ($this->services->has($id)) {
            //si existe el servicio lo devolvemos
            return $this->services->get($id);
        }
        //si no existe debemos crearlo
        //buscamos el servicio en el contenedor de servicios
        if (!$this->definitioManager->hasService($id)) {
            throw new IndexNotDefinedException(sprintf('No existe el servicio "%s"', $id));
        }

        $config = $this->definitioManager->getService($id)->getConfig();

        //retorna la instancia recien creada
        return $this->di->newInstance($id, $config);
    }

    public function has($id)
    {
        return $this->services->has($id);
    }

    public function set($id, $object)
    {
        $this->services->replace($id, $object);
        //y lo agregamos a las definiciones. (solo será a gregado si no existe)
        $this->definitioManager->addService(new Service($id, array(
                    'class' => get_class($object)
                )));
    }

    public function getParameter($id)
    {
        if ($this->hasParameter($id)) {
            return $this->definitioManager->getParam($id)->getValue();
        } else {
            return NULL;
        }
    }

    public function hasParameter($id)
    {
        return $this->definitioManager->hasParam($id);
    }

    public function getDefinitionManager()
    {
        return $this->definitioManager;
    }

}



namespace KumbiaPHP\Di;

use KumbiaPHP\Di\Container\Container;


interface DependencyInjectionInterface
{

    
    public function newInstance($id, $config);

    
    public function setContainer(Container $container);
}




namespace KumbiaPHP\Di;

use \ReflectionClass;
use KumbiaPHP\Di\Container\Container;
use KumbiaPHP\Di\Exception\DiException;
use KumbiaPHP\Di\DependencyInjectionInterface;
use KumbiaPHP\Di\Exception\IndexNotDefinedException;


class DependencyInjection implements DependencyInjectionInterface
{

    
    protected $container;

    
    private $queue = array();

    
    private $isQueue = FALSE;

    
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    
    public function newInstance($id, $config)
    {
        if (!isset($config['class'])) {
            throw new IndexNotDefinedException("No se Encontró el indice \"class\" en la definicón del servicio \"$id\"");
        }

        $reflection = new ReflectionClass($config['class']);

        if (isset($config['factory'])) {
            $method = $config['factory']['method'];
            if (isset($config['factory']['argument'])) {
                $instance = $this->callFactory($reflection, $method, $config['factory']['argument']);
            } else {
                $instance = $this->callFactory($reflection, $method);
            }
        } else {

            if (isset($config['construct'])) {
                $arguments = $this->getArgumentsFromConstruct($id, $config);
            } else {
                $arguments = array();
            }

            //verificamos si ya se creó una instancia en una retrollamada del
            //metodo injectObjectIntoServicesQueue
            if ($this->container->has($id)) {
                return $this->container->get($id);
            }

            $instance = $reflection->newInstanceArgs($arguments);
        }
        //agregamos la instancia del objeto al contenedor.
        $this->container->set($id, $instance);

        $this->injectObjectIntoServicesQueue();

        if (isset($config['call'])) {
            $this->setOtherDependencies($id, $instance, $config['call']);
        }
        
        return $instance;
    }

    
    protected function getArgumentsFromConstruct($id, array $config)
    {
        $args = array();
        //lo agregamos a la cola hasta que se creen los servicios del
        //que depende
        $this->addToQueue($id, $config);

        if (is_array($config['construct'])) {
            foreach ($config['construct'] as $serviceOrParameter) {
                if ('@' === $serviceOrParameter[0]) {//si comienza con @ es un servicio lo que solicita
                    $args[] = $this->container->get(substr($serviceOrParameter, 1));
                } else { //si no comienza por arroba es un parametro lo que solicita
                    $args[] = $this->container->getParameter($serviceOrParameter);
                }
            }
        } else {
            if ('@' === $config['construct'][0]) {//si comienza con @ es un servicio lo que solicita
                $args[] = $this->container->get(substr($config['construct'], 1));
            } else { //si no comienza por arroba es un parametro lo que solicita
                $args[] = $this->container->getParameter($config['construct']);
            }
        }
        //al tener los servicios que necesitamos
        //quitamos al servicio en construccion de la cola
        $this->removeToQueue($id);
        return $args;
    }

    
    protected function setOtherDependencies($id, $object, array $calls)
    {
        foreach ($calls as $method => $serviceOrParameter) {
            if ('@' === $serviceOrParameter[0]) {//si comienza con @ es un servicio lo que solicita
                $object->$method($this->container->get(substr($serviceOrParameter, 1)));
            } else { //si no comienza por arroba es un parametro lo que solicita
                $object->$method($this->container->getParameter($serviceOrParameter));
            }
        }
    }

    
    protected function injectObjectIntoServicesQueue()
    {
        $this->isQueue = TRUE;
        foreach ($this->queue as $id => $config) {
            $this->newInstance($id, $config);
        }
        $this->isQueue = FALSE;
    }

    protected function inQueue($id)
    {
        return isset($this->queue[$id]);
    }

    
    protected function addToQueue($id, $config)
    {
        //si el servicio actual aparece en la cola de servicios
        //indica que dicho servicio tiene una dependencia a un servicio 
        //que depende de este, por lo que hay una dependencia circular.
        if (!$this->isQueue && $this->inQueue($id)) {
            throw new \LogicException("Se ha Detectado una Dependencia Circular entre Servicios");
        }
        $this->queue[$id] = $config;
    }

    
    protected function removeToQueue($id)
    {
        if ($this->inQueue($id)) {
            unset($this->queue[$id]);
        }
    }

    
    protected function callFactory(\ReflectionClass $class, $method, $argument = NULL)
    {
        if (!$class->hasMethod($method)) {
            throw new DiException("No existe el Método \"$method\" en la clase \"{$class->name}\"");
        }

        $method = $class->getMethod($method);

        if (!$method->isStatic()) {
            throw new DiException("El Método \"$method\" de la clase \"{$class->name}\" debe ser Estático");
        }

        if ('@' === $argument[0]) {//si comienza con @ es un servicio lo que solicita
            $argument = $this->container->get(substr($argument, 1));
        } elseif ($argument) { //si no comienza por arroba es un parametro lo que solicita
            $argument = $this->container->getParameter($argument);
        }

        $class = $method->invoke(NULL, $argument);

        if (!is_object($class)) {
            throw new DiException("El Método \"$method\" de la clase \"{$class->name}\" debe retornar un Objeto");
        }

        return $class;
    }

}



namespace KumbiaPHP\Di\Definition;

use KumbiaPHP\Di\Definition\DefinitionInterface;


class Parameter implements DefinitionInterface
{

    protected $id;
    protected $value;

    function __construct($id, $value)
    {
        $this->id = $id;
        $this->value = $value;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

}




namespace KumbiaPHP\Di\Definition;


interface DefinitionInterface
{
    public function getId();
}



namespace KumbiaPHP\Di\Definition;

use KumbiaPHP\Di\Definition\DefinitionInterface;


class Service implements DefinitionInterface
{

    
    protected $id;
    
    protected $config;

    public function __construct($id, $config)
    {
        $this->id = $id;
        $this->config = $config;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }

}



namespace KumbiaPHP\Di\Definition;

use KumbiaPHP\Di\Definition\DefinitionInterface;
use KumbiaPHP\Di\Definition\Service;


class DefinitionManager
{

    
    protected $services;

    
    protected $parameters;

    
    public function __construct()
    {
        $this->services = array();
        $this->parameters = array();
    }

    
    public function hasService($id)
    {
        return isset($this->services[$id]);
    }

    
    public function getService($id)
    {
        return $this->hasService($id) ? $this->services[$id] : NULL;
    }

    
    public function hasParam($id)
    {
        return isset($this->parameters[$id]);
    }

    
    public function getParam($id)
    {
        return $this->hasParam($id) ? $this->parameters[$id] : NULL;
    }

    
    public function addService(DefinitionInterface $definition)
    {
        if (!$this->hasService($definition->getId())) {
            $this->services[$definition->getId()] = $definition;
        }
        return $this;
    }

    
    public function addParam(DefinitionInterface $param)
    {
        if (!$this->hasParam($param->getId())) {
            $this->parameters[$param->getId()] = $param;
        }
        return $this;
    }

    
    public function getSerivces()
    {
        return $this->services;
    }

    
    public function getParams()
    {
        return $this->parameters;
    }

}




namespace KumbiaPHP\Kernel\Config;

use KumbiaPHP\Kernel\AppContext;
use KumbiaPHP\Kernel\Collection;


class ConfigReader
{

    
    protected $config;
    private $sectionsValid = array('config', 'parameters');

    public function __construct(AppContext $app)
    {
        $this->config = $this->compile($app);
    }

    
    protected function compile(AppContext $app)
    {
        $section['config'] = new Collection();
        $section['services'] = new Collection();
        $section['parameters'] = new Collection();

        $dirs = array_merge($app->getNamespaces(), array_values($app->getModules()), array($app->getAppPath()));

        foreach (array_unique($dirs) as $namespace => $dir) {
            if (is_numeric($namespace)) {
                $configFile = rtrim($dir, '/') . '/config/config.ini';
                $servicesFile = rtrim($dir, '/') . '/config/services.ini';
            } else {
                $configFile = rtrim($dir, '/') . '/' . $namespace . '/config/config.ini';
                $servicesFile = rtrim($dir, '/') . '/' . $namespace . '/config/services.ini';
            }

            if (is_file($configFile)) {
                foreach (parse_ini_file($configFile, TRUE) as $sectionType => $values) {

                    if (in_array($sectionType, $this->sectionsValid)) {
                        foreach ($values as $index => $v) {
                            $section[$sectionType]->set($index, $v);
                        }
                    }
                }
            }
            if (is_file($servicesFile)) {
                foreach (parse_ini_file($servicesFile, TRUE) as $serviceName => $config) {
                    $section['services']->set($serviceName, $config);
                }
            }
        }

        $section = $this->explodeIndexes($section);

        unset($section['config']); //esta seccion esta disponible en parameters con el prefio config.*

        return new Collection($section);
    }

    public function getConfig()
    {
        return $this->config;
    }

    
    protected function explodeIndexes(array $section)
    {
        foreach ($section['config']->all() as $key => $value) {
            $explode = explode('.', $key);
            //si hay un punto y el valor delante del punto
            //es el nombre de un servicio existente
            if (count($explode) > 1 && $section['services']->has($explode[0])) {
                //le asignamos el nuevo valor al parametro
                //que usará ese servicio
                if ($section['parameters']->has($explode[1])) {
                    $section['parameters']->set($explode[1], $value);
                }
            } else {
                $section['parameters']->set('config.' . $key, $value);
            }
        }
        return $section;
    }

}



namespace KumbiaPHP\Kernel;

use KumbiaPHP\Kernel\Request;


class AppContext
{

    
    protected $baseUrl;

    
    protected $appPath;

    
    protected $modulesPath;

    
    protected $currentUrl;

    
    protected $modules;

    
    protected $namespaces;

    
    protected $currentModule;

    
    protected $currentController;

    
    protected $currentAction;

    
    protected $inProduction;

    
    public function __construct(Request $request, $inProduction, $appPath, $modules, $namespaces)
    {
        $this->baseUrl = $request->getBaseUrl();
        $this->inProduction = $inProduction;
        $this->appPath = $appPath;
        $this->currentUrl = $request->getRequestUrl();
        $this->modulesPath = $appPath . 'modules/';
        $this->modules = $modules;
        $this->namespaces = $namespaces;
    }

    
    public function setRequest(Request $request)
    {
        $this->currentUrl = $request->getRequestUrl();
    }

    
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    
    public function getAppPath()
    {
        return $this->appPath;
    }

    
    public function getCurrentUrl()
    {
        return $this->currentUrl;
    }

    
    public function getModulesPath()
    {
        return $this->modulesPath;
    }

    
    public function getNamespaces()
    {
        return $this->namespaces;
    }

    
    public function getModules($route = NULL)
    {
        if ($route) {
            return isset($this->modules[$route]) ? $this->modules[$route] : NULL;
        } else {
            return $this->modules;
        }
    }

    
    public function getCurrentModule()
    {
        return $this->currentModule;
    }

    
    public function setCurrentModule($currentModule)
    {
        $this->currentModule = $currentModule;
    }

    
    public function getCurrentController()
    {
        return $this->currentController;
    }

    
    public function setCurrentController($currentController)
    {
        $this->currentController = $currentController;
    }

    
    public function getCurrentAction()
    {
        return $this->currentController;
    }

    
    public function setCurrentAction($currentAction)
    {
        $this->currentAction = $currentAction;
    }

    public function createUrl($parameters = FALSE)
    {
        if ('/' !== $this->currentModule) {
            $url = $this->currentModule . '/' . $this->currentController . '/' . $this->currentAction;
        } else {
            $url = $this->currentController . '/' . $this->currentAction;
        }

        $url = $this->toSmallCase($url);

        if ($parameters) {
            $url .= substr($this->currentUrl, strlen($url) + 1);
        }

        return rtrim($url, '/');
    }

    
    public function InProduction()
    {
        return $this->inProduction;
    }

    public function getControllerUrl()
    {
        return $this->getBaseUrl() . trim($this->currentModule, '/') . '/' . $this->toSmallCase($this->currentController);
    }

    protected function toSmallCase($string)
    {
        $string[0] = strtolower($string[0]);

        return strtolower(preg_replace('/([A-Z])/', "_$1", $string));
    }

}





namespace KumbiaPHP\Kernel;


class Collection implements \Serializable
{

    
    protected $params;

    
    function __construct(array $params = array())
    {
        $this->params = $params;
    }

    
    public function has($key)
    {
        return array_key_exists($key, $this->params);
    }

    
    public function get($key, $default = NULL)
    {
        return $this->has($key) ? $this->params[$key] : $default;
    }

    
    public function set($key, $value)
    {
        $this->params[$key] = $value;
    }

    
    public function all()
    {
        return $this->params;
    }

    
    public function count()
    {
        return count($this->params);
    }

    
    public function delete($key)
    {
        if ($this->has($key)) {
            unset($this->params[$key]);
        }
    }

    
    public function clear()
    {
        $this->params = array();
    }

    
    public function serialize()
    {
        return serialize($this->params);
    }

    
    public function unserialize($serialized)
    {
        $this->params = unserialize($serialized);
    }

    
    public function keys()
    {
        return array_keys($this->params);
    }

    
    public function getInt($key, $default = 0)
    {
        return (int) $this->get($key, $default, $deep);
    }

    
    public function getDigits($key, $default = '')
    {
        return preg_replace('/[^[:digit:]]/', '', $this->get($key, $default));
    }

    
    public function getAlnum($key, $default = '')
    {
        return preg_replace('/[^[:alnum:]]/', '', $this->get($key, $default));
    }

    
    public function getAlpha($key, $default = '')
    {
        return preg_replace('/[^[:alpha:]]/', '', $this->get($key, $default));
    }

}



namespace KumbiaPHP\Kernel;

use KumbiaPHP\Kernel\Session\SessionInterface;
use KumbiaPHP\Kernel\AppContext;
use KumbiaPHP\Kernel\Collection;


class Request
{

    
    public $server;

    
    public $request;

    
    public $query;

    
    public $cookies;

    
    public $files;

    
    protected $app;

    
    private $baseUrl;

    
    protected $content = FALSE;

    
    public function __construct()
    {
        $this->server = new Collection($_SERVER);
        $this->request = new Collection($_POST);
        $this->query = new Collection($_GET);
        $this->cookies = new Collection($_COOKIE);
        $this->files = new Collection($_FILES);

        //este fix es para permitir tener en el request los valores para peticiones
        //PUT y DELETE, ya que php no ofrece una forma facil de obtenerlos
        //actualmente.
        if (0 === strpos($this->server->get('CONTENT_TYPE'), 'application/x-www-form-urlencoded')
                && in_array($this->getMethod(), array('PUT', 'DELETE'))
        ) {
            parse_str($this->getContent(), $data);
            $this->request = new Collection($data);
        } elseif (0 === strpos($this->server->get('CONTENT_TYPE'), 'application/json')) {
            //si los datos de la petición se envian en formato JSON
            //los convertimos en una arreglo.
            $this->request = new Collection((array) json_decode($this->getContent(), TRUE));
        }
    }

    
    public function get($key, $default = NULL)
    {
        //busca en request, si no existe busca en query sino existe busca en 
        //cookies, si no devuelve $default.
        return $this->request->get($key, $this->query->get($key, $this->cookies->get($key, $default)));
    }

    
    public function getAppContext()
    {
        return $this->app;
    }

    
    public function setAppContext(AppContext $app)
    {
        $this->app = $app;
    }

    
    public function getMethod()
    {
        return $this->server->get('REQUEST_METHOD', 'GET');
    }

    
    public function getClientIp()
    {
        return $this->server->get('REMOTE_ADDR');
    }

    
    public function isAjax()
    {
        return $this->server->get('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest';
    }

    
    public function isMethod($method)
    {
        return strtoupper($this->getMethod()) === strtoupper($method);
    }

    
    public function getBaseUrl()
    {
        if (!$this->baseUrl) {
            $this->baseUrl = $this->createBaseUrl();
        }
        return $this->baseUrl;
    }

    
    public function getRequestUrl()
    {
        return $this->query->get('_url', '/');
    }

    
    public function getContent()
    {
        if (FALSE === $this->content) {
            $this->content = file_get_contents('php://input');
        }
        return $this->content;
    }

    
    private function createBaseUrl()
    {
        $uri = $this->server->get('REQUEST_URI');
        if ($qString = $this->server->get('QUERY_STRING')) {
            return substr(urldecode($uri), 0, - strlen($qString) + 6);
        } else {
            return $uri;
        }
    }

}

