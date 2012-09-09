<?php
require_once '../../vendor/autoload.php';

use KumbiaPHP\Kernel\Kernel;

/**
 * Description of AppKernel
 *
 * @author manuel
 */
class AppKernel extends Kernel
{

    protected function registerNamespaces()
    {
        return array(
            'modules' => __DIR__ . '/modules/',
            'KumbiaPHP' => __DIR__ . '/../../vendor/kumbiaphp/kumbiaphp/src/',
        );
    }

    protected function registerRoutes()
    {
        $routes = array(
            '/' => __DIR__ . '/modules/Index/',
        );

        if (!$this->production) {
            $routes['/demo/rest']     = __DIR__ . '/modules/Demos/Rest/';
            $routes['/demo/router']   = __DIR__ . '/modules/Demos/Router/';
            $routes['/demo/vistas']   = __DIR__ . '/modules/Demos/Vistas/';
            $routes['/demo/modelos']  = __DIR__ . '/modules/Demos/Modelos/';
            $routes['/demo/upload']   = __DIR__ . '/modules/Demos/SubiendoArchivos/';
            $routes['/admin']   = __DIR__ . '/modules/Demos/Seguridad/';
        }
        return $routes;
    }

}