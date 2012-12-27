<?php

/* @var $loader Composer\Autoload\ClassLoader */
$loader = require_once __DIR__ . '/../../vendor/autoload.php';

use K2\Kernel\Kernel;
use K2\Kernel\App;

/**
 * Description of AppKernel
 *
 * @author manuel
 */
class AppKernel extends Kernel
{

    protected function registerModules()
    {
        $modules = array(
            'K2' => __DIR__ . '/../../vendor/kumbiaphp/core/src/',
            'Index' => __DIR__ . '/modules/',
        );

        if (!$this->production) {
            $modules['Demos/Rest'] = __DIR__ . '/modules/';
            $modules['Demos/Router'] = __DIR__ . '/modules/';
            $modules['Demos/Vistas'] = __DIR__ . '/modules/';
            $modules['Demos/Modelos'] = __DIR__ . '/modules/';
            $modules['Demos/SubiendoArchivos'] = __DIR__ . '/modules/';
            $modules['Demos/Seguridad'] = __DIR__ . '/modules/';
        }

        return $modules;
    }

    protected function registerRoutes()
    {
        $routes = array(
            '/' => 'Index',
        );

        if (!$this->production) {
            $routes['/demo/rest'] = 'Demos/Rest';
            $routes['/demo/router'] = 'Demos/Router';
            $routes['/demo/vistas'] = 'Demos/Vistas';
            $routes['/demo/modelos'] = 'Demos/Modelos';
            $routes['/demo/upload'] = 'Demos/SubiendoArchivos';
            $routes['/admin'] = 'Demos/Seguridad';
        }

        return $routes;
    }

}


App::setLoader($loader);

//acá podemos incluir rutas y prefijos al autoloader
//$loader->add('PHPExcel', __DIR__ . '../../vendor/');