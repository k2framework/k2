<?php

require_once '../../core/KumbiaPHP/Kernel/Kernel.php';

use KumbiaPHP\Loader\Autoload;
use KumbiaPHP\Kernel\Kernel;

/**
 * Description of AppKernel
 *
 * @author manuel
 */
class AppKernel extends Kernel
{

    //put your code here
    protected function registerNamespaces()
    {
        return array(
            'KumbiaPHP' => __DIR__ . '/../../core/',
            'Index' => __DIR__ . '/modules/',
        );
    }

    protected function registerModules()
    {
        return array(
            //'Nuevos' => __DIR__ . '/modules/Usuarios/Clientes/',
        );
    }

}