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
        return array(
            '/demo/rest' => __DIR__ . '/modules/Demos/Rest/',
            '/demo/router' => __DIR__ . '/modules/Demos/Router/',
            '/' => __DIR__ . '/modules/Index/',
        );
    }

}