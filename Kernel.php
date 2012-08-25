<?php

namespace KumbiaPHP\Kernel;

use KumbiaPHP\Kernel\KernelInterface;
//use KumbiaPHP\Kernel\Session\Session;
use KumbiaPHP\Kernel\Controller\ControllerResolver;

/**
 * Description of Kernel
 *
 * @author manuel
 */
abstract class Kernel implements KernelInterface
{

    protected $modules;

    public function __construct()
    {
        \PSR0\Autoload::install($this->getModulesAutoload());
    }

    //put your code here
    public function execute(Request $request)
    {
        $resolver = new ControllerResolver($this->getModules(), $this->getDefaultModule());

        list($controller, $action, $params) = $resolver->getController($request);

        $response = call_user_func_array(array($controller, $action), $params);

        if (!$response instanceof Response) {
            throw new \LogicException("La accion $action del controlador " . get_class($controller) . " Debe retornar una instancia de Response");
        }
        return $response;
    }

    abstract protected function registerModules();

    public function getModules()
    {
        if (!$this->modules) {
            $this->modules = $this->registerModules();
        }
        return $this->modules;
    }

    public function getModulesAutoload()
    {
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
    public function getDefaultModule()
    {
        return key($this->getModules());
    }

}