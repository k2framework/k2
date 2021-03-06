<?php

use K2\Kernel\App;
use K2\Kernel\Config\ConfigReader;

$loader = require_once __DIR__ . '/../../vendor/autoload.php';
/*
 */
###### obtenemos la url de la petición ############
$_url = isset($_GET['_url']) ? $_GET['_url'] : '/';

###### Creamos el PUBLIC_PATH ############
if (isset($_SERVER['QUERY_STRING'])) {
    $uri = $_SERVER['REQUEST_URI'];
    if (false !== ($pos = strpos($_SERVER['REQUEST_URI'], '?'))) {
        $uri = substr($_SERVER['REQUEST_URI'], 0, $pos);
    }
    define('PUBLIC_PATH', str_replace($_url, '/', urldecode($uri)));
    unset($uri);
} else {
    define('PUBLIC_PATH', isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');
}

App::setLoader($loader);

if (PRODUCTION) {
    error_reporting(0);
    ini_set('display_errors', 'Off');
} else {
    error_reporting(-1);
    ini_set('display_errors', 'On');
}

/**
 * Permite crear una ruta hasta un paquete instalado en vendor
 * @param string $package nombre del paquete, como se colocó en el composer.json
 * @param string $targetDir el target-dir usado por el paquete en su composer.json
 * @param string $file nombre del archivo php que contiene la configuración, por defecto config.php
 * @return string
 */
function composerPath($package, $targetDir, $file = 'config.php')
{
    return APP_PATH . '../../vendor/' . trim($package) . '/' . trim($targetDir) . '/' . $file;
}

require_once APP_PATH . 'config/modules.php';
