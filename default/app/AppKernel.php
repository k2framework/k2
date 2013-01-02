<?php

/* @var $loader Composer\Autoload\ClassLoader */
$loader = require_once __DIR__ . '/../../vendor/autoload.php';
//establecemos el loader en la clase App
\K2\Kernel\App::setLoader($loader);

use K2\Kernel\Kernel;

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

//establecemos la ruta a vendor en el loader por defecto.
//sin embargo es mejor registrar las libs y modulos que estén en vendor, ya que así
//el autoload las cargará más rapido.
$loader->add(null, __DIR__ . '/../../vendor/');
//acá podemos incluir rutas y prefijos al autoloader
//$loader->add('PHPExcel', __DIR__ . '/../../vendor/');