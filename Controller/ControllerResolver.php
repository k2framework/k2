<?php

namespace KumbiaPHP\Kernel\Controller;

use KumbiaPHP\Kernel\KernelInterface;
use KumbiaPHP\Kernel\Request;
use KumbiaPHP\Kernel\Exception\NotFoundException;
use KumbiaPHP\Kernel\Controller\Controller;

/**
 * Description of ControllerResolver
 *
 * @author manuel
 */
class ControllerResolver
{

    protected $kernel;
    protected $modulesPath;
    protected $module;
    protected $controller;
    protected $contShortName;
    protected $action;

    function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->modulesPath = $this->kernel->getAppPath() . 'modules/';
    }

    public function getController()
    {
        $controllerFound = FALSE;
        $module = $this->kernel->getDefaultModule();
        $controller = $this->kernel->getDefaultController();
        $action = $this->kernel->getDefaultAction();
        $params = array();

        $uri = explode('/', trim($this->kernel->getRequest()->getRequestUri(), '/'));

        //primero obtengo el modulo.
        if (current($uri)) {
            if (array_key_exists(current($uri), $this->kernel->getModules())) {
                //si concuerda con un modulo, es un modulo.
                $module = current($uri);
                next($uri);
            } elseif (!$this->isController($module, $this->camelcase(current($uri)))) {
                //si no es ni modulo ni controller, lanzo la excepcion.
                throw new NotFoundException(sprintf("El primer patron de la Ruta <b>%s</b> No Coincide con ningun Módulo", current($uri)), 404);
            }
        }
        //ahora obtengo el controlador
        if (current($uri)) {
            $controller = $this->camelcase(current($uri));
            if (!$this->isController($module, $controller)) {
                throw new NotFoundException(sprintf("El controlador <b>%s</b> para el Módulo <b>%s</b> no Existe", $controller, $module), 404);
            }
            next($uri);
        }
        //luego obtenemos la acción
        if (current($uri)) {
            $action = $this->camelcase(current($uri), TRUE);
            next($uri);
        }
        //por ultimo los parametros
        if (current($uri)) {
            $params = array_slice($uri, key($uri));
        }

        $this->module = $module;
        $this->contShortName = $controller;
        $this->action = $action;

        return $this->createController($module, $controller, $action, $params);
    }

    /**
     * Convierte la cadena con espacios o guión bajo en notacion camelcase
     *
     * @param string $s cadena a convertir
     * @param boolean $firstLower indica si es lower camelcase
     * @return string
     * */
    protected function camelcase($string, $firstLower = FALSE)
    {
        // Notacion lowerCamelCase
        $string = str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower($string))));
        if ($firstLower) {
            $string[0] = strtolower($string[0]);
            return $string;
        } else {
            return $string;
        }
    }

    protected function isController($module, $controller)
    {
        $modules = $this->kernel->getModules();
        $path = rtrim($modules[$module], '/'); // . '/' . $module;
        //var_dump("{$path}/Controller/{$controller}Controller.php");die;
        return is_file("{$path}/Controller/{$controller}Controller.php");
    }

    protected function createController($module, $controller, $action, $params)
    {
        $modules = $this->kernel->getModules();
        $path = rtrim($modules[$module], '/'); // . '/' . $module;
        $path = rtrim(substr($path, strlen($this->modulesPath)), '/');
        $controllerName = $controller . 'Controller';
        $controllerClass = str_replace('/', '\\', $path . '/Controller/') . $controllerName;

        $this->controller = new $controllerClass($this->kernel->getContainer());

        if (!$this->controller instanceof \KumbiaPHP\Kernel\Controller\Controller) {
            throw new NotFoundException(sprintf("El controlador <b>%s</b> debe extender de <b>%s</b>", $controllerName, 'KumbiaPHP\\Kernel\\Controller\\Controller'), 404);
        }

        if (!method_exists($this->controller, $action)) {
            throw new NotFoundException(sprintf("No exite el metodo <b>%s</b> en el controlador <b>%s</b>", $action, $controllerName), 404);
        }

        //Obteniendo el metodo
        $reflectionMethod = new \ReflectionMethod($this->controller, $action);

        //el nombre del metodo debe ser exactamente igual al camelcases
        //de la porcion de url
        if ($reflectionMethod->getName() !== $action) {
            throw new NotFoundException(sprintf("No exite el metodo <b>%s</b> en el controlador <b>%s</b>", $action, $controllerName), 404);
        }

        //se verifica que el metodo sea public
        if (!$reflectionMethod->isPublic()) {
            throw new NotFoundException(sprintf("Éstas Tratando de acceder a un metodo no publico <b>%s</b> en el controlador <b>%s</b>", $action, $controller), 404);
        }

        if (count($params) < $reflectionMethod->getNumberOfRequiredParameters() ||
                count($params) > $reflectionMethod->getNumberOfParameters()) {
            throw new NotFoundException(sprintf("Número de parámetros erróneo para ejecutar la acción <b>%s</b> en el controlador <b>%s</b>", $action, $controller), 404);
        }

        return array($this->controller, $action, $params);
    }

    public function getPublicProperties()
    {
        return get_object_vars($this->controller);
    }

    public function getView()
    {
        $view = $this->getParamValue('view');

        if ($view === NULL) {
            return NULL;
        }
        return $this->getModulePath() . '/View/' . $this->contShortName . '/' . $view . '.phtml';
    }

    public function getTemplate()
    {
        $template = $this->getParamValue('template');

        if ($template === NULL) {
            return NULL;
        }

        $dirTemplatesApp = $this->kernel->getAppPath() . 'view/templates/';

        return $dirTemplatesApp . $template . '.phtml';
    }

    public function getModulePath()
    {
        $modules = $this->kernel->getModules();
        return $modules[$this->module];
    }

    protected function getParamValue($propertie)
    {
        $reflection = new \ReflectionClass($this->controller);

        //obtengo el parametro del controlador.
        $propertie = $reflection->getProperty($propertie);

        //lo hago accesible para poderlo leer
        $propertie->setAccessible(true);

        //y retorno su valor
        return $propertie->getValue($this->controller);
    }

}