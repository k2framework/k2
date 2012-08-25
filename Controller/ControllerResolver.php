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

    function __construct($defaultModule = 'Home', $defaultController = 'Index', $defaultAction = 'index')
    {
        $this->defaultModule = $defaultModule;
        $this->defaultController = $defaultController;
        $this->defaultAction = $defaultAction;
    }

    public function getController(Request $request)
    {
        $controllerFound = FALSE;
        $controller = $this->defaultController;
        $action = $this->defaultAction;
        $aguments = array();

        $this->modulesPath = $request->getAppPath() . 'modules/';
        $currentPath = NULL;

        $uri = explode('/', ltrim($request->getRequestUri(), '/'));

        while (current($uri)) {
            $current = $this->camelcase(current($uri)); //obtengo el token en CamelCase
            if ($this->isController($currentPath, $current)) {
                $controller = "{$current}Controller";
                array_shift($uri);
                $controllerFound = TRUE;
                break;
            } else {
                if (!is_dir($this->modulesPath . $currentPath)) {
                    throw new NotFoundException("No se encontró un controlador en está ruta", 404);
                }
                $currentPath .= $current . '/'; //lo añado a la ruta
                array_shift($uri);
            }
        }
        if (!$controllerFound) {
            throw new NotFoundException("No se encontró un controlador en está ruta", 404);
        }
        if (current($uri)) {
            $action = $this->camelcase(current($uri), TRUE);
            array_shift($uri);
        }
        $arguments = $uri;

        return $this->createController($currentPath, $controller, $action, $arguments);
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

    protected function isController($path, $controller)
    {
        $path = rtrim($path, '/');
        return is_file("{$this->modulesPath}{$path}/Controller/{$controller}Controller.php");
    }

    protected function createController($pathToController, $controllerName, $action, $params)
    {
        $controllerClass = str_replace('/', '\\', $pathToController . 'Controller/') . $controllerName;

        $controllerObject = new $controllerClass();

        if (!method_exists($controllerObject, $action)) {
            throw new NotFoundException("No exite el metodo {$action} para el controlador {$controllerClass}");
        }

        //Obteniendo el metodo
        $reflectionMethod = new \ReflectionMethod($controllerObject, $action);

        //se verifica que el metodo sea public
        if (!$reflectionMethod->isPublic()) {
            throw new NotFoundException("Éstas Tratando de acceder a un metodo no publico {$action} en el controlador {$controllerClass}");
        }

        if (count($params) < $reflectionMethod->getNumberOfRequiredParameters() ||
                count($params) > $reflectionMethod->getNumberOfParameters()) {
            throw new NotFoundException("Número de parámetros erróneo para ejecutar la acción \"{$action}\" en el controlador \"$controllerClass\"");
        }

        return array(new $controllerClass(), $action, $params);
    }

}