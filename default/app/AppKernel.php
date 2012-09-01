<?php
require_once '../../core/KumbiaPHP/Kernel/Kernel.php';

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
            'KumbiaPHP' => __DIR__ . '/../../core/',
            'ActiveRecord' => __DIR__ . '/../../core/',
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