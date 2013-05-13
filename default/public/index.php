<?php

use K2\Kernel\Kernel;
use K2\Kernel\Request;

define('START_TIME', microtime(1));

###### Especificamos si el proyecto está en producción o no ############
define('PRODUCTION', false);

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

###### Arrancamos la config inical del Framework ############
require_once '../app/bootstrap.php';

###### Arrancamos y ejecutamos el FW ############
$app = new Kernel();

$app->execute(new Request($_url))->send();