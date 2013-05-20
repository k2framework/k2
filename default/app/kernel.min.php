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

use K2\Kernel\AppContext;
use K2\Kernel\Session\SessionInterface;


class Request
{

    
    protected $app;

    
    protected $content = false;

    
    protected $locale;
    protected $uri;

    
    public function __construct($uri)
    {
        //este fix es para permitir tener en el request los valores para peticiones
        //PUT y DELETE, ya que php no ofrece una forma facil de obtenerlos
        //actualmente.
        if (0 === strpos($this->server('CONTENT_TYPE'), 'application/x-www-form-urlencoded') && in_array($this->getMethod(), array('PUT', 'DELETE'))
        ) {
            parse_str($this->getContent(), $_REQUEST);
        } elseif (0 === strpos($this->server('CONTENT_TYPE'), 'application/json')) {
            //si los datos de la petición se envian en formato JSON
            //los convertimos en una arreglo.
            $_REQUEST = json_decode($this->getContent(), true);
        }

        $this->uri = $uri;
    }

    
    public function get($key, $default = null)
    {
        return filter_has_var(INPUT_GET, $key) ? filter_input(INPUT_GET, $key, FILTER_SANITIZE_STRING) : $default;
    }

    
    public function post($key, $default = null)
    {
        return filter_has_var(INPUT_POST, $key) ? $_POST[$key] : $default;
    }

    
    public function request($key, $default = null)
    {
        return array_key_exists($key, $_REQUEST) ? $_REQUEST[$key] : $default;
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
        return $this->server('REQUEST_METHOD', 'GET');
    }

    
    public function getClientIp()
    {
        return $this->server('REMOTE_ADDR');
    }

    
    public function isAjax()
    {
        return 'XMLHttpRequest' === $this->server('HTTP_X_REQUESTED_WITH');
    }

    
    public function isMethod($method)
    {
        return strtoupper($this->getMethod()) === strtoupper($method);
    }

    
    public function getRequestUrl()
    {
        return $this->uri;
    }

    
    public function getContent()
    {
        if (false === $this->content) {
            $this->content = file_get_contents('php://input');
        }
        return $this->content;
    }

    public function __clone()
    {
        $this->__construct($this->uri);
    }

    
    public function getLocale()
    {
        return $this->locale;
    }

    
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function server($key, $default = null)
    {
        return array_key_exists($key, $_SERVER) ? $_SERVER[$key] : $default;
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

use K2\Di\Exception\IndexNotDefinedException;
use K2\Di\Exception\DiException;


class Container implements \ArrayAccess
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
        $instance = $this->getInstance($id);

        if (!is_object($instance)) {
            throw new DiException("La función que crea el servicio $id bebe retornar un objeto");
        }

        return $instance;
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

    
    public function set($id, \Closure $function, $singleton = true)
    {
        $this->definitions['services'][$id] = array($function, $singleton);
        return $this;
    }

    
    public function setFromArray(array $services)
    {
        $this->definitions['services'] = $services + $this->definitions['services'];
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
        if ($value instanceof \Closure) {
            $this->set($offset, $value);
        } else {
            $this->setParameter($offset, $value);
        }
    }

    public function offsetUnset($offset)
    {
        //nada por ahora
    }

    protected function getInstance($id)
    {
        $data = $this->definitions['services'][$id];

        if ($data instanceof \Closure) {
            return $this->services[$id] = $data($this);
        }

        if (is_array($data)) {

            if (!$data[0] instanceof \Closure) {
                throw new DiException("No se reconoce el valor para la definición del servicio $id");
            }

            $instance = $data[0]($this);

            if (isset($data[1]) && true === $data[1]) {
                $this->services[$id] = $instance;
            }

            return $instance;
        }

        throw new DiException("No se reconoce el valor para la definición del servicio $id");
    }

}

namespace K2\EventDispatcher;

use K2\Di\Container\Container;


class EventDispatcher
{

    
    protected $listeners = array();

    
    protected $container;

    
    protected $sorted = array();

    
    public function __construct(Container $container = null)
    {
        if ($container) {
            $this->setContainer($container);
        }
    }

    
    public function setContainer(Container $container)
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
use K2\Di\Container\Container;
use K2\Kernel\Exception\NotFoundException;
use K2\Kernel\App;


class ControllerResolver
{

    
    protected $container;

    
    protected $module;

    
    protected $controller;

    
    protected $controllerName;

    
    protected $action;

    
    protected $parameters;

    public function __construct(Container $con)
    {
        $this->container = $con;

        $context = App::getContext();

        $this->module = $context['module'];
        $this->controllerUrl = $context['controller'];
        $this->action = $context['action'] . '_action';
        $this->parameters = $context['parameters'];
        if ('/logout' === App::getRequest()->getRequestUrl()) {
            throw new NotFoundException("La ruta \"/logout\" no concuerda con ningún módulo ni controlador en la App");
        }
    }

    
    public function getController()
    {
        if ($this->controller instanceof Controller) {
            return $this->controller;
        }

        $module = App::getContext('module');

        $this->controllerName = App::getContext('controller') . 'Controller';

        //uno el namespace y el nombre del controlador.
        $controllerClass = $module['namespace'] . "\\Controller\\{$this->controllerName}";

//        if ($this->module->hasChildren()) {
//            $children = $this->module->getChildren();
//            $childrenControllerClass = $children->getNamespace() . "\\Controller\\{$this->controllerName}";
//            if (class_exists($childrenControllerClass)) {
//                $controllerClass = $childrenControllerClass;
//                $this->module = $children;
//            }
//        }

        if (!class_exists($controllerClass)) {
            throw new NotFoundException(sprintf("No existe el controlador \"%s\" en la ruta \"%s/Controller/%s.php\"", $this->controllerName, $module['path'], $this->controllerName));
        }

        $reflectionClass = new ReflectionClass($controllerClass);

        $this->controller = $reflectionClass->newInstance();

        return $this->controller;
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
                    App::setContext(array('action' => false));
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
                App::setContext(array('action' => $result));
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

}

namespace K2\Kernel\Controller;

use K2\Kernel\App;
use K2\Kernel\Response;
use K2\Kernel\Router\RouterInterface;


class Controller
{

    
    protected $view;

    
    protected $response;

    
    protected $cache = null;

    
    protected $limitParams = true;

    
    protected $parameters;

    protected function renderNotFound($message)
    {
        throw new \K2\Kernel\Exception\NotFoundException($message);
    }

    
    protected function getRequest()
    {
        return App::getRequest();
    }

    
    protected function getRouter()
    {
        return App::get('router');
    }

    
    final public function setView($view)
    {
        $this->view = $view;
    }

    
    final public function setResponse($response)
    {
        $this->response = $response;
    }

    
    final public function getView()
    {
        return $this->view;
    }

    
    final public function getResponse()
    {
        return $this->response;
    }

    
    final public function cache($time = false)
    {
        $this->cache = $time;
    }

    final public function getCache()
    {
        return $this->cache;
    }

    
    protected function render($view, array $params = array(), $time = null)
    {
        return App::get('view')->render($view, array(
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

use K2\Kernel\App;
use K2\Kernel\Event\K2Events;
use K2\Di\Container\Container;
use K2\Kernel\Event\RequestEvent;
use K2\Kernel\Event\ResponseEvent;
use K2\Kernel\Event\ExceptionEvent;
use K2\EventDispatcher\EventDispatcher;
use K2\Kernel\Exception\ExceptionHandler;
use K2\Kernel\Controller\ControllerResolver;


class Kernel
{

    const MASTER_REQUEST = 1;
    const SUB_REQUEST = 2;

    
    protected $dispatcher;

    
    protected $locales;

    
    public function __construct()
    {
        App::getLoader()->add(null, APP_PATH . '/modules/');

        $this->initContainer();

        $this->initDispatcher();

        $this->initModules();
    }

    
    public function execute(Request $request, $type = self::MASTER_REQUEST)
    {
        try {
            App::setRequest($request);
            return $this->_execute($request, $type);
            App::terminate();
        } catch (\Exception $e) {
            return $this->exception($e);
        }
    }

    private function _execute(Request $request, $type = Kernel::MASTER_REQUEST)
    {
        $this->dispatcher->dispatch(K2Events::REQUEST, $event = new RequestEvent($request));

        if (!$event->hasResponse()) {
            //creamos el resolver.
            $resolver = new ControllerResolver(App::get('container'));

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
        $controller = $resolver->getController();
        //como la acción no devolvió respuesta, debemos
        //obtener la vista y el template establecidos en el controlador
        //para pasarlos al servicio view, y este construya la respuesta
        //llamamos al render del servicio "view" y esté nos devolverá
        //una instancia de response con la respuesta creada
        return App::get('view')->render($controller->getView(), array(
                    'response' => $controller->getResponse(),
                    'time' => $controller->getCache(),
                    'params' => get_object_vars($controller),
        ));
    }

    private function exception(\Exception $e)
    {
        $event = new ExceptionEvent($e, App::getRequest());
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
        $event = new ResponseEvent(App::getRequest(), $response);
        //ejecutamos el evento response.
        $this->dispatcher->dispatch(K2Events::RESPONSE, $event);
        //retornamos la respuesta
        return $event->getResponse();
    }

    
    protected function initContainer(array $config = array())
    {
        App::setContainer($container = new Container());

        $container->setInstance('app.kernel', $this);

        $this->readConfig();
    }

    protected function initModules()
    {
        $container = App::get('container');
        foreach (App::modules() as $name => $config) {
            $container->setFromArray($config['services']);
            foreach ($config['listeners'] as $event => $listeners) {
                foreach ($listeners as $priority => $listener) {
                    $this->dispatcher->addListener($event, $listener, $priority);
                }
            }
        }
        foreach (App::modules() as $name => $config) {
            if (is_callable($config['init'])) {
                call_user_func($config['init'], $container);
            }
        }
    }

    
    protected function initDispatcher()
    {
        $this->dispatcher = new EventDispatcher(App::get('container'));
        App::get('container')->setInstance('event.dispatcher', $this->dispatcher);
    }

    protected function readConfig()
    {
        $config = Config\Reader::read('config');

        foreach ($config as $section => $values) {
            App::get('container')->setParameter($section, $values);
        }

        if (isset($config['config']['locales'])) {
            $this->locales = explode(',', $config['config']['locales']);
        }
    }

    
    public function getModule($url, $recursive = true)
    {
        if (count($this->locales) && $recursive) {
            $_url = explode('/', trim($url, '/'));
            $locale = array_shift($_url);
            if (in_array($locale, $this->locales)) {
                App::getRequest()->setLocale($locale);
                return $this->getModule('/' . join('/', $_url), false);
            } else {
                App::getRequest()->setLocale($this->locales[0]);
            }
        }

        $routes = array_keys(App::routes());

        usort($routes, function($a, $b) {
                    return strlen($a) > strlen($b) ? -1 : 1;
                }
        );

        foreach ($routes as $route) {
            if (0 === strpos($url, $route)) {
                if ('/' === $route) {
                    return array($route, App::getModule(App::getRoute('/')), $url);
                } elseif ('/' === substr($url, strlen($route), 1) || strlen($url) === strlen($route)) {
                    return array($route, App::getModule(App::getRoute($route)), $url);
                }
            }
        }
        return false;
    }

}

namespace K2\Kernel;

use K2\Kernel\Request;
use K2\Kernel\AppContext;
use Composer\Autoload\ClassLoader;
use K2\Di\Container\Container;
use K2\Security\Auth\User\UserInterface;

class App
{

    
    protected static $container;

    
    protected static $loader;
    protected static $modules;
    protected static $routes;
    protected static $request = array();
    protected static $context = array();

    
    public static function setContainer(Container $container)
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

    
    public static function setRequest(Request $request)
    {
        return static::$request[] = $request;
    }

    
    public static function getRequest()
    {
        return end(static::$request);
    }

    
    public static function terminate()
    {
        unset(static::$context[static::getRequest()->getRequestUrl()]);
        array_pop(static::$request);
    }

    public static function setContext(array $data)
    {
        $uri = static::getRequest()->getRequestUrl();
        if (isset(static::$context[$uri])) {
            static::$context[$uri] = array_merge(static::$context[$uri], $data);
        } else {
            static::$context[$uri] = $data;
        }
    }

    public static function getContext($index = null)
    {
        if (null === $index) {
            return static::$context[static::getRequest()->getRequestUrl()];
        } else {
            if (isset(static::$context[static::getRequest()->getRequestUrl()])) {
                $context = static::$context[static::getRequest()->getRequestUrl()];
                return isset($context[$index]) ? $context[$index] : null;
            }
        }
    }

    
    public static function getUser()
    {
        if (!is_object($token = self::$container->get('security')->getToken())) {
            return null;
        }
        return $token->getUser();
    }

    
    public static function requestUrl()
    {
        return self::getRequest()->getRequestUrl();
    }

    public static function modules(array $modules = null)
    {
        if (null === $modules) {
            return static::$modules;
        } else {
            foreach ($modules as $index => $module) {
                static::$modules[$module['name']] = $module + array(
                    'parameters' => array(),
                    'services' => array(),
                    'listeners' => array(),
                    'init' => null,
                );
                //si el indice no es numerico, agregamos el mismo a las rutas
                if (!is_numeric($index)) {
                    static::$routes[$index] = $module['name'];
                }
            }
        }
    }

    public static function routes()
    {
        return static::$routes;
    }

    public static function getModule($name, $index = null, $throw = true)
    {
        if (isset(static::$modules[$name])) {
            if (null !== $index) {
                if (array_key_exists(static::$modules[$name], $index)) {
                    return static::$modules[$name][$index];
                }
                if ($throw) {
                    throw new \InvalidArgumentException(sprintf('No existe el indice %s en la configuración del Módulo %s', $index, $name));
                }

                return null;
            } else {
                return static::$modules[$name];
            }
        }
        if ($throw) {
            throw new \InvalidArgumentException(sprintf('No existe el Módulo %s', $name));
        }

        return null;
    }

    public static function getRoute($route, $throw = true)
    {
        if (isset(static::$routes[$route])) {
            return static::$routes[$route];
        }
        if ($throw) {
            throw new \InvalidArgumentException(sprintf('No existe la ruta %s', $route));
        }

        return null;
    }

    public static function prefix($module, $throw = true)
    {
        if (!in_array($module, static::$routes)) {
            if ($throw) {
                throw new \InvalidArgumentException(sprintf('No existe un prefijo de ruta para el módulo %s', $module));
            }
            return null;
        }

        return array_search($module, static::$routes);
    }

}
