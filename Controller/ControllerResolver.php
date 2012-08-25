<?php

namespace KumbiaPHP\Kernel\Controller;

use KumbiaPHP\Kernel\Request;
use KumbiaPHP\Kernel\Exception\NotFoundException;

/**
 * Description of ControllerResolver
 *
 * @author manuel
 */
class ControllerResolver
{

    protected $defaultModule;
    protected $defaultController;
    protected $defaultAction;
    protected $modulesPath;
    protected $modules;

    function __construct($modules, $defaultModule = '', $defaultController = 'Index', $defaultAction = 'index')
    {
        $this->defaultModule = $defaultModule;
        $this->defaultController = $defaultController;
        $this->defaultAction = $defaultAction;
        $this->modules = $modules;
        //var_dump($this->modules);
    }

    public function getController(Request $request)
    {
        $controllerFound = FALSE;
        $module = $this->defaultModule;
        $controller = $this->defaultController;
        $action = $this->defaultAction;
        $params = array();

        $this->modulesPath = $request->getAppPath() . 'modules/';

        $uri = explode('/', trim($request->getRequestUri(), '/'));

        //primero obtengo el modulo.
        if (current($uri)) {
            $module = current($uri);
            if (!array_key_exists($module, $this->modules)) {
                throw new NotFoundException(sprintf("El primer patron de la Ruta <b>%s</b> No Coincide con ningun Módulo", $module), 404);
            }
        }
        //ahora obtengo el controlador
        if (next($uri)) {
            $controller = $this->camelcase(current($uri));
            if (!$this->isController($module, $controller)) {
                throw new NotFoundException(sprintf("El controlador <b>%s</b> para el Módulo <b>%s</b> no Existe", $controller, $module), 404);
            }
        }
        //luego obtenemos la acción
        if (next($uri)) {
            $action = $this->camelcase(current($uri), TRUE);
        }
        //por ultimo los parametros
        if (next($uri)) {
            $params = array_slice($uri, key($uri));
        }

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
        if ($firstLower) {
            $string = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
            $string[0] = strtolower($string[0]);
            return $string;
        }
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }

    protected function isController($module, $controller)
    {
        $path = rtrim($this->modules[$module], '/'); // . '/' . $module;
        //var_dump("{$path}/Controller/{$controller}Controller.php");die;
        return is_file("{$path}/Controller/{$controller}Controller.php");
    }

    protected function createController($module, $controller, $action, $params)
    {
        $path = rtrim($this->modules[$module], '/'); // . '/' . $module;
        $path = rtrim(substr($path, strlen($this->modulesPath)), '/');
        $controllerName = $controller . 'Controller';
        $controllerClass = str_replace('/', '\\', $path . '/Controller/') . $controllerName;

        $controllerObject = new $controllerClass();

        if (!method_exists($controllerObject, $action)) {
            throw new NotFoundException(sprintf("No exite el metodo <b>%s</b> en el controlador <b>%s</b>", $action, $controllerName), 404);
        }

        //Obteniendo el metodo
        $reflectionMethod = new \ReflectionMethod($controllerObject, $action);

        //se verifica que el metodo sea public
        if (!$reflectionMethod->isPublic()) {
            throw new NotFoundException(sprintf("Éstas Tratando de acceder a un metodo no publico <b>%s</b> en el controlador <b>%s</b>", $action, $controller), 404);
        }

        if (count($params) < $reflectionMethod->getNumberOfRequiredParameters() ||
                count($params) > $reflectionMethod->getNumberOfParameters()) {
            throw new NotFoundException(sprintf("Número de parámetros erróneo para ejecutar la acción <b>%s</b> en el controlador <b>%s</b>", $action, $controller), 404);
        }

        return array($controllerObject, $action, $params);
    }

}