<?php

use K2\Kernel\App;
use K2\Kernel\Exception\ExceptionHandler;

$loader = require_once __DIR__ . '/../../vendor/autoload.php';

define('APP_PATH', __DIR__);

App::setLoader($loader);

ExceptionHandler::handle();

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