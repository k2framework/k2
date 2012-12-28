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
            new \K2\K2Module(),
            new \Index\IndexModule(),
        );

        if (!$this->production) {
            $modules[] = new Demos\Modelos\ModelosModule();
            $modules[] = new Demos\Rest\RestModule();
            $modules[] = new Demos\Router\RouterModule();
            $modules[] = new Demos\SubiendoArchivos\ArchivosModule();
            $modules[] = new Demos\Seguridad\SeguridadModule();
            $modules[] = new Demos\Vistas\VistasModule();
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