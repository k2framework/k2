<?php

use K2\Kernel\App;
use K2\Kernel\Exception\ExceptionHandler;

$loader = require_once __DIR__ . '/../../vendor/autoload.php';
/*
 */
###### Especificamos si el proyecto está en producción o no ############
define('PRODUCTION', false);
ExceptionHandler::handle(false);
/*
 */
define('APP_PATH', __DIR__);
define('START_TIME', microtime(1));

###### obtenemos la url de la petición ############
$_url = isset($_GET['_url']) ? $_GET['_url'] : '/';

###### Creamos el PUBLIC_PATH ############
if ($_SERVER['QUERY_STRING']) {
    $uri = $_SERVER['REQUEST_URI'];
    if (false !== ($pos = strpos($_SERVER['REQUEST_URI'], '?'))) {
        $uri = substr($_SERVER['REQUEST_URI'], 0, $pos);
    }
    define('PUBLIC_PATH', str_replace($_url, '/', urldecode($uri)));
    unset($uri);
} else {
    define('PUBLIC_PATH', $_SERVER['REQUEST_URI']);
}

App::setLoader($loader);


if (PRODUCTION) {
    error_reporting(0);
    ini_set('display_errors', 'Off');
} else {
    error_reporting(-1);
    ini_set('display_errors', 'On');
}

App::modules(array(
    require_once __DIR__ . '/../../vendor/k2/core/src/K2/config.php',
));

require_once APP_PATH . '/config/app.php';