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

    public function __construct()
    {
        \PSR0\Autoload::install($this->registerModules());
    }

    //put your code here
    public function execute(Request $request)
    {
        $resolver = new ControllerResolver();

        list($controller, $action, $params) = $resolver->getController($request);

        $response = call_user_func_array(array($controller, $action), $params);

        if (!$response instanceof Response) {
            throw new \LogicException("La accion $action del controlador " . get_class($controller) . " Debe retornar una instancia de Response");
        }
        return $response;
    }

    abstract protected function registerModules();
}