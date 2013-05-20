<?php

namespace Demos\Modelos\Controller;

use Demos\Modelos\Model\Estados;
use Scaffold\Controller\ScaffoldController;

class estadosController extends ScaffoldController
{
    
    protected function beforeFilter()
    {
        $this->model = new Estados();
    }
}