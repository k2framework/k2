<?php

use K2\Kernel\Kernel;
use K2\Kernel\Request;

define('START_TIME', microtime(1));
###### Especificamos si el proyecto está en producción o no ############
define('PRODUCTION', false);
/*
 */
define('APP_PATH', rtrim(__DIR__, '/') . '/app/');
/*
 */
//require_once '../app/kernel.min.php'; //usar las clases compiladas (mejora velocidad)
###### Arrancamos la config inical del Framework ############
require_once '../app/bootstrap.php';

###### Arrancamos y ejecutamos el FW ############
$app = new Kernel();

$app->execute(new Request($_url))->send();