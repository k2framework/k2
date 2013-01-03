<?php


namespace K2\Kernel;


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
        return array_keys($this->all());
    }

    
    public function getInt($key, $default = 0)
    {
        return (int) $this->get($key, $default);
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

namespace K2\Kernel;

use K2\Kernel\Collection;

class CookiesCollection extends Collection
{

    
    public function has($key)
    {
        return array_key_exists($key, $_COOKIE);
    }

    
    public function get($key, $default = NULL)
    {
        return $this->has($key) ? $_COOKIE[$key] : $default;
    }

    
    public function set($key, $value, $expire = 0)
    {
        setcookie($key, $value, $expire);
    }

    
    public function all()
    {
        return (array) $_COOKIE;
    }

    
    public function count()
    {
        return count($_COOKIE);
    }

    
    public function delete($key)
    {
        if ($this->has($key)) {
            $this->set($key, false);
        }
    }

    
    public function clear()
    {
        foreach ($this->keys() as $cookie) {
            $this->delete($cookie);
        }
    }

}

namespace K2\Kernel;

use K2\Kernel\File;

class FilesCollection
{

    
    protected $params;

    public function __construct()
    {
        foreach ((array) $_FILES as $name => $file) {
            if (isset($file['name']) && is_array($file['name'])) {
                foreach (array_keys($file['name']) as $key) {
                    $this->set($key, new File(array(
                                'name' => $file['name'][$key],
                                'type' => $file['type'][$key],
                                'tmp_name' => $file['tmp_name'][$key],
                                'error' => $file['error'][$key],
                                'size' => $file['size'][$key],
                            )), $name);
                }
            } else {
                $this->set($name, new File($file));
            }
        }
    }

    
    public function has($key, $form = null)
    {
        if (null === $form) {
            return array_key_exists($key, $this->params);
        } else {
            return isset($this->params[$form]) && isset($this->params[$form][$key]);
        }
    }

    
    public function get($key, $form = null)
    {
        if (null === $form && $this->has($key)) {
            return $this->params[$key];
        } elseif ($this->has($key, $form)) {
            return $this->params[$form][$key];
        } else {
            return null;
        }
    }

    
    public function set($key, File $file, $form = null)
    {
        if (null === $form) {
            $this->params[$key] = $file;
        } else {
            $this->params[$form][$key] = $file;
        }
    }

    
    public function all()
    {
        return $this->params;
    }

    
    public function count()
    {
        return count($this->params);
    }

    
    public function delete($key, $form = null)
    {
        if ($this->has($key)) {
            unset($this->params[$key]);
        }
    }

    
    public function clear()
    {
        $this->params = array();
    }

    
    public function keys()
    {
        return array_keys($this->all());
    }

}

namespace K2\Kernel;

use K2\Kernel\Collection;
use K2\Kernel\AppContext;
use K2\Kernel\FilesCollection;
use K2\Kernel\CookiesCollection;
use K2\Kernel\Session\SessionInterface;


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
    protected $locale;

    
    public function __construct($baseUrl = NULL)
    {
        $this->server = new Collection($_SERVER);
        $this->request = new Collection($_POST);
        $this->query = new Collection($_GET);
        $this->cookies = new CookiesCollection();
        $this->files = new FilesCollection();

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

        if ($baseUrl) {
            $this->baseUrl = $baseUrl;
        } else {
            $this->baseUrl = $this->createBaseUrl();
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

    public function __clone()
    {
        $this->__construct($this->getBaseUrl());
    }

    
    public function getLocale()
    {
        return $this->locale;
    }

    
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    
    private function createBaseUrl()
    {
        $uri = $this->server->get('REQUEST_URI');
        if ($qString = $this->server->get('QUERY_STRING')) {
            if (false !== $pos = strpos($uri, '?')) {
                $uri = substr($uri, 0, $pos);
            }
            return str_replace($this->getRequestUrl(), '/', urldecode($uri));
        } else {
            return $uri;
        }
    }

}

namespace K2\Kernel;

use K2\Kernel\Request;
use K2\Kernel\Exception\NotFoundException;


class AppContext
{

    
    protected $appPath;

    
    protected $modulesPath;

    
    protected $modules;

    
    protected $routes;

    
    protected $currentModule;

    
    protected $currentModuleUrl;

    
    protected $currentController;

    
    protected $currentAction;

    
    protected $currentParameters;

    
    protected $inProduction;

    
    protected $requestType;

    
    protected $request;

    
    public function __construct($inProduction, $appPath, $modules, $routes)
    {
        $this->inProduction = $inProduction;
        $this->appPath = $appPath;
        $this->modulesPath = rtrim($appPath, '/') . '/modules/';
        $this->modules = $modules;
        $this->routes = $routes;
    }

    
    public function setRequest(Request $request)
    {
        $request->setAppContext($this);
        $this->request = $request;
        return $this;
    }

    public function setLocales($locales = null)
    {
        $this->locales = explode(',', $locales);
        return $this;
    }

    
    public function setRequestType($type)
    {
        $this->requestType = $type;
        return $this;
    }

    
    public function getRequestType()
    {
        return $this->requestType;
    }

    
    public function getBaseUrl()
    {
        return $this->request->getBaseUrl();
    }

    
    public function getAppPath()
    {
        return $this->appPath;
    }

    
    public function getRequestUrl()
    {
        return $this->request->getRequestUrl();
    }

    
    public function getPath($module)
    {
        if (isset($this->modules[$module])) {
            return rtrim($this->modules[$module], '/') . "/{$module}/";
        } else {
            return NULL;
        }
    }

    
    public function getCurrentModule()
    {
        return $this->currentModule;
    }

    
    public function setCurrentModule($currentModule)
    {
        $this->currentModule = $currentModule;
        return $this;
    }

    
    public function getCurrentController()
    {
        return $this->currentController;
    }

    
    public function setCurrentController($currentController)
    {
        $this->currentController = $currentController;
        return $this;
    }

    
    public function getCurrentAction()
    {
        return $this->currentAction;
    }

    
    public function setCurrentAction($currentAction)
    {
        $this->currentAction = $currentAction;
        return $this;
    }

    
    public function getCurrentParameters()
    {
        return $this->currentParameters;
    }

    
    public function setCurrentParameters(array $currentParameters = array())
    {
        $this->currentParameters = $currentParameters;
        return $this;
    }

    
    public function getCurrentUrl($parameters = FALSE)
    {
        $url = $this->createUrl("{$this->currentModule}:{$this->currentController}/{$this->currentAction}");
        if ($parameters && count($this->currentParameters)) {
            $url .= '/' . join('/', $this->currentParameters);
        }
        return $url;
    }

    
    public function InProduction()
    {
        return $this->inProduction;
    }

    
    public function getControllerUrl($action = null)
    {
        return rtrim($this->createUrl("{$this->currentModule}:{$this->currentController}/{$action}"), '/');
    }

    
    public function getCurrentModuleUrl()
    {
        return $this->currentModuleUrl;
    }

    
    public function setCurrentModuleUrl($currentModuleUrl)
    {
        $this->currentModuleUrl = $currentModuleUrl;
        return $this;
    }

    
    public function createUrl($url, $baseUrl = true)
    {
        $url = explode(':', $url);
        if (count($url) > 1) {
            if (!$route = array_search($url[0], $this->routes)) {
                throw new NotFoundException("No Existe el módulo {$url[0]}, no se pudo crear la url");
            }
            $url = ltrim(trim($route, '/') . '/' . $url[1], '/');
        } else {
            $url = ltrim($url[0], '/');
        }
        //si se usa locale, lo añadimos a la url.
        $this->request->getLocale() && $url = $this->request->getLocale() . '/' . $url;
        return $baseUrl ? $this->request->getBaseUrl() . $url : $url;
    }
}



namespace K2\Kernel\Config;

use K2\Kernel\AppContext;


class ConfigReader
{

    
    protected $config;

//    private $sectionsValid = array('config', 'parameters');

    public function __construct(AppContext $app)
    {
        $configFile = $app->getAppPath() . '/config/config.php';
        if ($app->inProduction()) {
            if (is_file($configFile)) {
                $this->config = require $configFile;
                return;
            } else {
                $this->config = $this->compile($app);
                $config = PHP_EOL . PHP_EOL . 'return '
                        . var_export($this->config, true);
                file_put_contents($configFile, "<?php$config;");
            }
        } else {
            $this->config = $this->compile($app);
            if (is_writable($configFile)) {
                unlink($configFile);
            }
        }
    }

    
    protected function compile(AppContext $app)
    {
        $parameters = array();
        $services = array();

        $dirs = array_merge($app->getModules(), array('app' => dirname($app->getAppPath())));

        foreach ($dirs as $namespace => $dir) {
            $configFile = rtrim($dir, '/') . '/' . $namespace . '/config/config.ini';
            $servicesFile = rtrim($dir, '/') . '/' . $namespace . '/config/services.ini';

            if (is_file($configFile)) {
                foreach (parse_ini_file($configFile, true) as $sectionType => $values) {

                    foreach ($values as $index => $v) {
                        $parameters[$sectionType][$index] = $v;
                    }
                }
            }
            if (is_file($servicesFile)) {
                foreach (parse_ini_file($servicesFile, TRUE) as $serviceName => $config) {
                    if (isset($config['listen'])) {
                        foreach ($config['listen'] as $method => $event) {
                            $config['listen'][$method] = $event = explode(':', $event);
                            isset($event[1]) || $config['listen'][$method][1] = 0;
                        }
                    }
                    $services[$serviceName] = $config;
                }
            }
        }

        return $this->prepareAditionalConfig(array(
                    'parameters' => $parameters,
                    'services' => $services,
                ));
    }

    public function getConfig()
    {
        return $this->config;
    }

    
    protected function prepareAditionalConfig($configs)
    {
        //si se usa el routes lo añadimos al container
        if (isset($configs['parameters']['config']['routes'])) {
            $router = substr($configs['parameters']['config']['routes'], 1);

            //si es el router por defecto quien reescribirá las url
            if ('router' === $router) {
                //le añadimos un listener.
                $configs['services']['router']
                        ['listen']['rewrite'] = 'kumbia.request:1000'; //con priotidad 1000 para que sea el primero en ejecutarse.
            }
        }

        //si se estan usando locales y ningun módulo a establecido una definición para
        //el servicio translator, lo hacemos por acá.
        if (isset($configs['parameters']['config']['locales'])
                && !isset($configs['services']['translator'])) {
            $configs['services']['translator'] = array(
                'class' => 'K2\\Translation\\Translator',
            );
        }

        return $configs;
    }

}

namespace K2\Di\Container;


interface ContainerInterface extends \ArrayAccess
{

    
    public function get($id);

    
    public function has($id);

    
    public function hasInstance($id);

    
    public function getParameter($id);

    
    public function hasParameter($id);

    
    public function setParameter($id, $value);

    
    public function getDefinitions();
}


namespace K2\Di\Container;

use K2\Di\Container\ContainerInterface;
use K2\Di\Exception\IndexNotDefinedException;


class Container implements ContainerInterface
{

    
    protected $services;

    
    protected $definitions;

    public function __construct()
    {
        $this->services = array();
        $this->definitions = array(
            'parameters' => array(),
            'services' => array()
        );

        //agregamos al container como servicio.
        $this->setInstance('container', $this);
    }

    public function get($id)
    {

        //si no existe lanzamos la excepcion
        if (!$this->has($id)) {
            throw new IndexNotDefinedException(sprintf('No existe el servicio "%s"', $id));
        }
        //si existe el servicio y está creado lo devolvemos
        if ($this->hasInstance($id)) {
            return $this->services[$id];
        }
        //si existe pero no se ha creado, creamos la instancia
        $this->services[$id] = $this->definitions['services'][$id]($this);
                
        if (!is_object($this->services[$id])){
            throw new \K2\Di\Exception\DiException("La función que crea el servicio $id bebe retornar un objeto");
        }
        
        return $this->services[$id];
    }

    public function has($id)
    {
        return isset($this->definitions['services'][$id]);
    }

    public function hasInstance($id)
    {
        return isset($this->services[$id]);
    }

    
    public function setInstance($id, $object)
    {
        $this->services[$id] = $object;
        //y lo agregamos a las definiciones. (solo será a gregado si no existe)
        if (!isset($this->definitions['services'][$id])) {

            $this->definitions['services'][$id] = true;
        }
    }

    public function getParameter($id)
    {
        if ($this->hasParameter($id)) {
            return $this->definitions['parameters'][$id];
        } else {
            return NULL;
        }
    }

    public function hasParameter($id)
    {
        return array_key_exists($id, $this->definitions['parameters']);
    }

    public function getDefinitions()
    {
        return $this->definitions;
    }

    public function setParameter($id, $value)
    {
        $this->definitions['parameters'][$id] = $value;
        return $this;
    }

    
    public function set($id, \Closure $function)
    {
        $this->definitions['services'][$id] = $function;
        return $this;
    }

    
    public function offsetExists($offset)
    {
        return $this->has($offset) || $this->hasParameter($offset);
    }

    
    public function offsetGet($offset)
    {
        if ($this->has($offset)) {
            return $this->get($offset);
        } elseif ($this->hasParameter($offset)) {
            return $this->getParameter($offset);
        } else {
            return null;
        }
    }

    public function offsetSet($offset, $value)
    {
        if($value instanceof \Closure){
            $this->set($offset, $value);
        }else{
            $this->setParameter($offset, $value);
        }
    }

    public function offsetUnset($offset)
    {
        //nada por ahora
    }

}

namespace K2\EventDispatcher;

use K2\EventDispatcher\Event;
use K2\EventDispatcher\EventSubscriberInterface;


interface EventDispatcherInterface
{

    
    public function dispatch($eventName, Event $event = null);

    
    public function addListener($eventName, $listener, $priority = 0);

    
    public function addSubscriber(EventSubscriberInterface $subscriber);

    
    public function hasListeners($eventName);

    
    public function removeListener($eventName, $listener);

    public function getListeners($eventName);
}

namespace K2\EventDispatcher;

use K2\EventDispatcher\EventDispatcherInterface;
use K2\Di\Container\ContainerInterface;


class EventDispatcher implements EventDispatcherInterface
{

    
    protected $listeners = array();

    
    protected $container;

    
    protected $sorted = array();

    
    public function __construct(ContainerInterface $container = null)
    {
        if ($container) {
            $this->setContainer($container);
        }
    }

    
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
//        $definitions = $container->getDefinitions();
//        foreach ($definitions['services'] as $id => $config) {
//            if (isset($config['listen'])) {//si está escuchando eventos
//                foreach ($config['listen'] as $method => $params) {
//                    isset($params[1]) || $params[1] = 0; //si no existe la prioridad la seteamos a 0
//                    $this->addListener($params[0], array($id, $method), $params[1]);
//                }
//            } elseif (isset($config['subscriber'])) {
//                $this->addSubscriber($container->get($id));
//            }
//        }
    }

    public function dispatch($eventName, Event $event = null)
    {
        if (!$this->hasListeners($eventName)) {
            return;
        }

        if (!$event) {
            $event = new Event();
        }

        foreach ($this->getListeners($eventName) as $listener) {
            call_user_func($listener, $event);
            if ($event->isPropagationStopped()) {
                return;
            }
        }
    }

    public function addListener($eventName, $listener, $priority = 0)
    {
        $this->listeners[$eventName][$priority][] = $listener;
        unset($this->sorted[$eventName]);
    }

    public function hasListeners($eventName)
    {
        return isset($this->listeners[$eventName]);
    }

    public function getListeners($eventName)
    {
        if (isset($this->sorted[$eventName])) {
            //si ya estan ordenados, solo devolvemos los listeners.
            return $this->sorted[$eventName];
        }

        //si no estan en el arreglo $sorted, lo creamos.
        $this->sortListeners($eventName);

        foreach ($this->sorted[$eventName] as $index => $listener) {
            if (!is_callable($listener)) {
                //si listener no es un funcion ó un objeto con un metodo que se pueda llamar
                //es porque estamos solicitando un servicio.
                //entonces convertirmos el listener en un objeto con un metodo que se
                //puedan llamar.
                $service = $this->container->get($listener[0]);
                $this->sorted[$eventName][$index][0] = $service;
            }
        }

        return $this->sorted[$eventName];
    }

    public function removeListener($eventName, $listener)
    {
        if ($this->hasListeners($eventName)) {
            foreach ($this->listeners[$eventName] as $priority => $listeners) {
                if (false !== ($key = array_search($listener, $listeners))) {
                    unset($this->listeners[$eventName][$priority][$key]);
                    unset($this->sorted[$eventName]);
                    return;
                }
            }
        }
    }

    protected function sortListeners($eventName)
    {
        if (isset($this->listeners[$eventName])) {
            krsort($this->listeners[$eventName]);
        }
        //unimos todos los listener que estan en prioridades diferentes.
        $this->sorted[$eventName] = call_user_func_array('array_merge', $this->listeners[$eventName]);
    }

    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $method => $params) {
            $params = (array) $params;
            isset($params[1]) || $params[1] = 0; //si no se pasa la prioridad, la creamos.
            //params[0] es el método del objeto a llamar.
            //params[1] es la prioridad.
            $this->addListener($params[0], array($subscriber, $method), $params[1]);
        }
    }

}

namespace K2\Kernel\Event;


final class K2Events
{

    const REQUEST = 'kumbia.request';
    const RESPONSE = 'kumbia.response';
    const EXCEPTION = 'kumbia.exception';

}

namespace K2\EventDispatcher;


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

namespace K2\Kernel\Event;

use K2\Kernel\Request;
use K2\Kernel\Response;
use K2\EventDispatcher\Event;


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

namespace K2\Kernel\Controller;

use \ReflectionClass;
use \ReflectionObject;
use K2\Kernel\Response;
use K2\Kernel\Event\ControllerEvent;
use K2\Di\Container\ContainerInterface;
use K2\Kernel\Exception\NotFoundException;


class ControllerResolver
{

    
    protected $container;

    
    protected $module;

    
    protected $controller;

    
    protected $controllerName;

    
    protected $action;

    
    protected $parameters;

    public function __construct(ContainerInterface $con)
    {
        $this->container = $con;

        $app = $con->get('app.context');

        $this->module = $app->getCurrentModule();
        $this->controllerUrl = $app->getCurrentController();
        $this->action = $app->getCurrentAction() . '_action';
        $this->parameters = $app->getCurrentParameters();
    }

    
    public function getController()
    {
        if ('/logout' === $this->module) {
            throw new NotFoundException(sprintf("La ruta \"%s\" no concuerda con ningún módulo ni controlador en la App", $this->module));
        }

        $app = $this->container->get('app.context');
        $this->controllerName = $app->getCurrentController() . 'Controller';
        //uno el namespace y el nombre del controlador.
        $controllerClass = str_replace('/', '\\', $this->module) . "\\Controller\\{$this->controllerName}";

        try {
            $reflectionClass = new ReflectionClass($controllerClass);
            if ($reflectionClass->getShortName() !== $this->controllerName) {
                throw new NotFoundException();
            }
        } catch (\Exception $e) {
            $modulePath = $app->getPath($this->module);
            throw new NotFoundException(sprintf("No existe el controlador \"%s\" en la ruta \"%sController/%s.php\"", $this->controllerName, $modulePath, $this->controllerName));
        }

        $this->controller = $reflectionClass->newInstanceArgs(array($this->container));
        $this->setViewDefault($app->getCurrentAction());
    }

    
    public function executeAction()
    {
        $this->getController();
        
        $controller = new ReflectionObject($this->controller);

        if (($response = $this->executeBeforeFilter($controller)) instanceof Response) {
            return $response;
        }

        if (false === $this->action) {
            return; //si el before devuelve false, es porque no queremos que se ejecute nuestra acción.
        }
        $this->validateAction($controller, $this->parameters);

        $response = call_user_func_array(array($this->controller, $this->action), $this->parameters);

        $this->executeAfterFilter($controller);

        return $response;
    }

    
    public function getPublicProperties()
    {
        return get_object_vars($this->controller);
    }

    
    protected function validateAction(\ReflectionObject $controller, array $params)
    {
        if ($controller->hasProperty('limitParams')) {
            $limitParams = $controller->getProperty('limitParams');
            $limitParams->setAccessible(true);
            $limitParams = $limitParams->getValue($this->controller);
        } else {
            $limitParams = true; //por defeto siempre limita los parametro
        }

        if ($controller->hasProperty('parameters')) {
            $parameters = $controller->getProperty('parameters');
            $parameters->setAccessible(true);
            $parameters->setValue($this->controller, $params);
        }
        //verificamos la existencia del metodo.
        if (!$controller->hasMethod($this->action)) {
            throw new NotFoundException(sprintf("No existe el metodo \"%s\" en el controlador \"%s\"", $this->action, $this->controllerName));
        }

        $reflectionMethod = $controller->getMethod($this->action);

        //el nombre del metodo debe ser exactamente igual al camelCase
        //de la porcion de url
        if ($reflectionMethod->getName() !== $this->action) {
            throw new NotFoundException(sprintf("No existe el metodo <b>%s</b> en el controlador \"%s\"", $this->action, $this->controllerName));
        }

        //se verifica que el metodo sea public
        if (!$reflectionMethod->isPublic()) {
            throw new NotFoundException(sprintf("Éstas Tratando de acceder a un metodo no publico \"%s\" en el controlador \"%s\"", $this->action, $this->controllerName));
        }

        
        if ($limitParams && (count($params) < $reflectionMethod->getNumberOfRequiredParameters() ||
                count($params) > $reflectionMethod->getNumberOfParameters())) {

            throw new NotFoundException(sprintf("Número de parámetros erróneo para ejecutar la acción \"%s\" en el controlador \"%sr\"", $this->action, $this->controllerName));
        }
    }

    
    protected function executeBeforeFilter(ReflectionObject $controller)
    {
        if ($controller->hasMethod('beforeFilter')) {
            $method = $controller->getMethod('beforeFilter');
            $method->setAccessible(true);

            if (null !== $result = $method->invoke($this->controller)) {
                if (false === $result) {
                    //si el resultado es false, es porque no queremos que se ejecute la acción
                    $this->action = false;
                    $this->container->get('app.context')->setCurrentAction(false);
                    return;
                }
                if ($result instanceof Response) {
                    return $result; //devolvemos el objeto Response.
                }
                if (!is_string($result)) {
                    throw new NotFoundException(sprintf("El método \"beforeFilter\" solo puede devolver un <b>false, una cadena, ó un objeto Response<b> en el Controlador \"%s\"", $this->controllerName));
                }
                if (!$controller->hasMethod($result)) {
                    throw new NotFoundException(sprintf("El método \"beforeFilter\" está devolviendo el nombre de una acción inexistente \"%s\" en el Controlador \"%s\"", $result, $this->controllerName));
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
            $method->setAccessible(true);
            $method->invoke($this->controller);
        }
    }

    
    public function callMethod($method)
    {
        $reflection = new \ReflectionClass($this->controller);

        if ($reflection->hasMethod($method)) {

            //obtengo el parametro del controlador.
            $method = $reflection->getMethod($method);

            //lo hago accesible para poderlo leer
            $method->setAccessible(true);

            //y retorno su valor
            return $method->invoke($this->controller);
        } else {
            return null;
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

namespace K2\Kernel\Controller;

use K2\Di\Container\ContainerInterface;
use K2\Kernel\Request;
use K2\Kernel\Router\Router;
use K2\Kernel\Response;


class Controller
{

    
    protected $container;

    
    protected $view;

    
    protected $template = 'default';

    
    protected $response;

    
    protected $cache = null;

    
    protected $limitParams = TRUE;

    
    protected $parameters;

    
    public final function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    protected function renderNotFound($message)
    {
        throw new \K2\Kernel\Exception\NotFoundException($message);
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

    
    final protected function setView($view, $template = false)
    {
        $this->view = $view;
        if ($template !== false) {
            $this->setTemplate($template);
        }
    }

    
    final protected function setResponse($response, $template = false)
    {
        $this->response = $response;
        if ($template !== false) {
            $this->setTemplate($template);
        }
    }

    
    final protected function setTemplate($template)
    {
        $this->template = $template;
    }

    
    final protected function getView()
    {
        return $this->view;
    }

    
    final protected function getResponse()
    {
        return $this->response;
    }

    
    final protected function getTemplate()
    {
        return $this->template;
    }

    
    final protected function cache($time = false)
    {
        $this->cache = $time;
    }

    final protected function getCache()
    {
        return $this->cache;
    }

    
    protected function render(array $params = array(), $time = null)
    {
        return $this->get('view')->render(array(
                    'template' => $this->getTemplate(),
                    'view' => $this->getView(),
                    'response' => $this->getResponse(),
                    'params' => $params,
                    'time' => $time,
                ));
    }

}

namespace K2\Kernel;

use K2\Kernel\Collection;


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

    
    protected function sendContent()
    {
        echo $this->content;
        while (ob_get_level()) {
            ob_end_flush(); //vamos limpiando y mostrando todos los niveles de buffer creados.
        }
    }

}

namespace K2\Kernel\Event;

use K2\Kernel\Event\RequestEvent;
use K2\Kernel\Request;
use K2\Kernel\Response;


class ResponseEvent extends RequestEvent
{

    protected $response;

    function __construct(Request $request, Response $response)
    {
        parent::__construct($request);
        $this->response = $response;
    }

    
    public function getResponse()
    {
        return $this->response;
    }

}

namespace K2\Kernel;

use K2\Kernel\Request;
use K2\Kernel\Response;
use K2\Di\Container\ContainerInterface;


interface KernelInterface
{

    const MASTER_REQUEST = 1;
    const SUB_REQUEST = 2;

    
    public function execute(Request $request, $type = KernelInterface::MASTER_REQUEST);
}


namespace K2\Kernel;

use K2\Kernel\AppContext;
use K2\Di\Container\Container;
use K2\Kernel\KernelInterface;
use K2\Kernel\Event\K2Events;
use K2\Kernel\Event\RequestEvent;
use K2\Kernel\Event\ResponseEvent;
use K2\Kernel\Config\ConfigReader;
use K2\Kernel\Event\ExceptionEvent;
use K2\EventDispatcher\EventDispatcher;
use K2\Kernel\Exception\ExceptionHandler;
use K2\Kernel\Exception\NotFoundException;
use K2\Kernel\Controller\ControllerResolver;


abstract class Kernel implements KernelInterface
{

    
    protected $modules;

    
    protected $routes;

    
    protected $container;

    
    protected $request;

    
    protected $dispatcher;

    
    protected $production;

    
    protected $appPath;

    
    protected $locales;

    
    public function __construct($production = FALSE)
    {
        ob_start(); //arrancamos el buffer de salida.
        $this->production = $production;

        App::getLoader()->add(null, $this->getAppPath() . '/modules/');

        ExceptionHandler::handle($this);

        if ($production) {
            error_reporting(0);
            ini_set('display_errors', 'Off');
        } else {
            error_reporting(-1);
            ini_set('display_errors', 'On');
        }

        $this->routes = $this->registerRoutes();
    }

    
    public function init(Request $request)
    {
        //creamos la instancia del AppContext
        $context = new AppContext($this->production, $this->getAppPath(), $this->modules, $this->routes);
        //leemos la config de la app
        //$config = new ConfigReader($context);
        //iniciamos el container con esa config
        $this->initContainer();
        //asignamos el kernel al container como un servicio
        $this->container->setInstance('app.kernel', $this);
        //iniciamos el dispatcher con esa config
        $this->initDispatcher();
        //inicializamos los modulos de la app.
        $this->initModules();
        //seteamos el contexto de la aplicación como servicio
        $this->container->setInstance('app.context', $context);
        //si se usan locales los añadimos.
        if (isset($this->container['config']['locales'])) {
            $this->locales = $this->container['config']['locales'];
        }
        $this->readConfig();
        //establecemos el Request en el AppContext
        $context->setRequest($request);
    }

    
    public function parseUrl()
    {
        $controller = 'index'; //controlador por defecto si no se especifica.
        $action = 'index'; //accion por defecto si no se especifica.
        $moduleUrl = '/';
        $params = array(); //parametros de la url, de existir.
        //obtenemos la url actual de la petición.
        $currentUrl = '/' . trim($this->request->getRequestUrl(), '/');

        list($moduleUrl, $module, $currentUrl) = $this->getModule($currentUrl);

        if (!$moduleUrl || !$module) {
            throw new NotFoundException(sprintf("La ruta \"%s\" no concuerda con ningún módulo ni controlador en la App", $currentUrl), 404);
        }

        if ($url = explode('/', trim(substr($currentUrl, strlen($moduleUrl)), '/'))) {

            //ahora obtengo el controlador
            if (current($url)) {
                //si no es un controlador lanzo la excepcion
                $controller = current($url);
                next($url);
            }
            //luego obtenemos la acción
            if (current($url)) {
                $action = current($url);
                next($url);
            }
            //por ultimo los parametros
            if (current($url)) {
                $params = array_slice($url, key($url));
            }
        }

        App::getContext()->setCurrentModule($module)
                ->setCurrentModuleUrl($moduleUrl)
                ->setCurrentController($controller)
                ->setCurrentAction($action)
                ->setCurrentParameters($params);
    }

    public function execute(Request $request, $type = Kernel::MASTER_REQUEST)
    {
        try {
            //verificamos el tipo de petición
            if (self::MASTER_REQUEST === $type) {
                return $this->_execute($request, $type);
            } else {
                //almacenamos en una variable temporal el request
                //original. y actualizamos el AppContext.
                //tambien el tipo de request
                $originalRequest = $this->request;
                $originalRequestType = $this->container->get('app.context')
                        ->getRequestType();
                $this->container->get('app.context')
                        ->setRequest($request)
                        ->setRequestType($type);

                $response = $this->_execute($request, $type);

                //Luego devolvemos el request original al kernel,
                //al AppContext, y el tipo de request
                $this->request = $originalRequest;
                $this->container->setInstance('request', $originalRequest);
                $this->container->get('app.context')
                        ->setRequest($originalRequest)
                        ->setRequestType($originalRequestType);

                return $response;
            }
        } catch (\Exception $e) {
            return $this->exception($e);
        }
    }

    private function _execute(Request $request, $type = Kernel::MASTER_REQUEST)
    {
        $this->request = $request;

        if (!$this->container) { //si no se ha creado el container lo creamos.
            $this->init($request);
            $this->container->get('app.context')->setRequestType($type);
        }
        //agregamos el request al container
        $this->container->setInstance('request', $this->request);

        //parseamos la url para obtener el modulo,controlador,accion,parametros
        $this->parseUrl();

        //ejecutamos el evento request
        $this->dispatcher->dispatch(K2Events::REQUEST, $event = new RequestEvent($request));

        if (!$event->hasResponse()) {

            //creamos el resolver.
            $resolver = new ControllerResolver($this->container);

            //ejecutamos la acción de controlador pasandole los parametros.
            $response = $resolver->executeAction();
            if (!$response instanceof Response) {
                $response = $this->createResponse($resolver);
            }
        } else {
            $response = $event->getResponse();
        }

        return $this->response($response);
    }

    
    private function createResponse(ControllerResolver $resolver)
    {
        //como la acción no devolvió respuesta, debemos
        //obtener la vista y el template establecidos en el controlador
        //para pasarlos al servicio view, y este construya la respuesta
        //llamamos al render del servicio "view" y esté nos devolverá
        //una instancia de response con la respuesta creada
        return $this->container->get('view')->render(array(
                    'template' => $resolver->callMethod('getTemplate'),
                    'view' => $resolver->callMethod('getView'),
                    'response' => $resolver->callMethod('getResponse'),
                    'time' => $resolver->callMethod('getCache'),
                    'params' => $resolver->getPublicProperties(), //nos devuelve las propiedades publicas del controlador
                ));
    }

    private function exception(\Exception $e)
    {
        $event = new ExceptionEvent($e, $this->request);
        $this->dispatcher->dispatch(K2Events::EXCEPTION, $event);

        if ($event->hasResponse()) {
            return $this->response($event->getResponse());
        }

        if ($this->production) {
            return ExceptionHandler::createException($e);
        }

        throw $e;
    }

    private function response(Response $response)
    {
        $event = new ResponseEvent($this->request, $response);
        //ejecutamos el evento response.
        $this->dispatcher->dispatch(K2Events::RESPONSE, $event);
        //retornamos la respuesta
        return $event->getResponse();
    }

    
    public function isProduction()
    {
        return $this->production;
    }

    
    abstract protected function registerModules();

    
    abstract protected function registerRoutes();

    
    public function getAppPath()
    {
        if (!$this->appPath) {
            $r = new \ReflectionObject($this);
            $this->appPath = dirname($r->getFileName()) . '/';
        }
        return $this->appPath;
    }

    
    protected function initContainer(array $config = array())
    {
        $this->container = new Container();
        $this->container->setParameter('app_dir', $this->getAppPath());
        App::setContainer($this->container);
    }

    protected function initModules()
    {
        $this->modules = (array) $this->registerModules();
        foreach ($this->modules as $name => $module) {
            $module->setContainer($this->container);
            $module->setEventDispatcher($this->dispatcher);
            $module->init();
        }
    }

    
    protected function initDispatcher()
    {
        $this->dispatcher = new EventDispatcher($this->container);
        $this->container->setInstance('event.dispatcher', $this->dispatcher);
    }

    protected function readConfig()
    {
        $config = Config\Reader::read('config');

        foreach ($config as $section => $values) {
            if ($this->container->hasParameter($section)) {
                $values = $this->mergeConfig($this->container->getParameter($section), $values);
            }
            $this->container->setParameter($section, $values);
        }
    }

    
    protected function getModule($url, $recursive = true)
    {
        if (count($this->locales) && $recursive) {
            $_url = explode('/', trim($url, '/'));
            $locale = array_shift($_url);
            if (in_array($locale, $this->locales)) {
                $this->request->setLocale($locale);
                return $this->getModule('/' . join('/', $_url), false);
            } else {
                $this->request->setLocale($this->locales[0]);
            }
        }

        if ('/logout' === $url) {
            return array($url, $url, $url);
        }

        $routes = array_keys($this->routes);

        usort($routes, function($a, $b) {
                    return strlen($a) > strlen($b) ? -1 : 1;
                }
        );

        foreach ($routes as $route) {
            if (0 === strpos($url, $route)) {
                if ('/' === $route) {
                    return array($route, $this->getRoutes('/'), $url);
                } elseif ('/' === substr($url, strlen($route), 1) || strlen($url) === strlen($route)) {
                    return array($route, $this->getRoutes($route), $url);
                }
            }
        }
        return false;
    }

    
    public function getModules($name = null)
    {
        if ($name) {
            foreach ($this->modules as $module) {
                if ($name === $module->getName()) {
                    return $module;
                }
            }
            return null;
        } else {
            return $this->modules;
        }
    }

    
    protected function getRoutes($route = null)
    {
        if ($route) {
            if (isset($this->routes[$route])) {
                foreach ($this->modules as $module) {
                    if ($this->routes[$route] === $module->getName()) {
                        return $this->routes[$route];
                    }
                }
            }
            return null;
        } else {
            return $this->routes;
        }
    }

    private function mergeConfig($config, $newConfig)
    {
        foreach ($newConfig as $key => $value) {
            if (array_key_exists($key, $config) && is_array($value)) {
                $config[$key] = $this->mergeConfig($config[$key], $newConfig[$key]);
            } else {
                $config[$key] = $value;
            }
        }

        return $config;
    }

}

namespace K2\Kernel;

use K2\Kernel\Request;
use K2\Kernel\AppContext;
use Composer\Autoload\ClassLoader;
use K2\Di\Container\ContainerInterface;
use K2\Security\Auth\User\UserInterface;

class App
{

    
    protected static $container;

    
    protected static $loader;

    
    public static function setContainer(ContainerInterface $container)
    {
        self::$container = $container;
    }

    
    public static function setLoader(ClassLoader $loader)
    {
        self::$loader = $loader;
    }

    
    public static function getLoader()
    {
        return self::$loader;
    }

    
    public static function get($service)
    {
        return self::$container->get($service);
    }

    
    public static function getParameter($parameter)
    {
        return self::$container->getParameter($parameter);
    }

    
    public static function getRequest()
    {
        return self::$container->get('request');
    }

    
    public static function getContext()
    {
        return self::$container->get('app.context');
    }

    
    public static function getUser()
    {
        if (!is_object($token = self::$container->get('security')->getToken())) {
            return null;
        }
        return $token->getUser();
    }

    
    public static function appPath()
    {
        return self::$container->getParameter('app_dir');
    }

    
    public static function requestUrl()
    {
        return self::getRequest()->getRequestUrl();
    }

    
    public static function baseUrl()
    {
        return self::getRequest()->getBaseUrl();
    }

}


namespace K2\Kernel;

use K2\Di\Container\Container;
use K2\EventDispatcher\EventDispatcherInterface;

class Module
{

    
    protected $container;

    
    protected $dispatcher;

    
    protected $path;
    protected $name;

    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    public function setEventDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function init()
    {
        
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        if (!$this->name) {
            $r = new \ReflectionObject($this);
            $this->name = str_replace('\\', '/', $r->getNamespaceName());
        }
        return $this->name;
    }

    public function getPath()
    {
        if (!$this->path) {
            $r = new \ReflectionObject($this);
            $this->path = dirname($r->getFileName()) . '/';
        }
        return $this->path;
    }

}
