<?php

define('START_TIME', microtime(1));

require_once '../app/kernel.min.php';
require_once '../app/AppKernel.php';

use KumbiaPHP\Kernel\Request;

$app = new AppKernel(false);

$app->execute(new Request())->send();