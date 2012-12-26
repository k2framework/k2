<?php
if (!in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1'))) {
    header('HTTP/1.0 403 Forbidden');
    exit();
}

define('START_TIME', microtime(1));

ob_start();

require_once '../app/AppKernel.php';

use K2\Kernel\Request;
use K2\Compiler\Compiler;

$compiler = new Compiler(__DIR__ . '/../app/kernel.min.php', TRUE);
$compiler->registerDirectories(array(
    'KumbiaPHP' => __DIR__ . '/../../vendor/kumbiaphp/core/src/',
));

$compiler->compile();

die("Se ha compilado el core del Framework");