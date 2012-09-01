<?php

require_once '../app/AppKernel.php';

use KumbiaPHP\Kernel\Request;

$app = new AppKernel(false);

$app->execute(new Request())->send();