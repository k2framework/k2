<?php

define('START_TIME', microtime(1));

require_once '../app/kernel.min.php';
require_once '../app/AppKernel.php';
//require_once '../app/AppCache.php';

use KumbiaPHP\Kernel\Request;

$app = new AppKernel(true);

//$app = new AppCache($app);

$app->execute(new Request())->send();