<?php

define('START_TIME', microtime(1));

//require_once __DIR__ . '/../app/kernel.min.php';
require_once __DIR__ . '/../app/AppKernel.php';

use K2\Kernel\Request;
use K2\Cache\AppCache;

$app = new AppKernel(false);

//$app = new AppCache($app);

$app->execute(new Request())->send();