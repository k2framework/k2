<?php
if (!in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1'))) {
    header('HTTP/1.0 403 Forbidden');
    exit();
}

ob_start();

$loader = require_once __DIR__ . '/../../vendor/autoload.php';

use K2\Compiler\Compiler;

$file = __DIR__ . '/../app/kernel.min.php';

$loader->add('K2\\', __DIR__ . '/k2/core/src/');

$compiler = new Compiler($loader, $file);

$compiler->compile();

die("Se ha compilado el core del Framework");