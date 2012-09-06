<?php

namespace Demos\Modelos\Controller;

use Demos\Modelos\Model\Usuarios;
use Scaffold\Controller\ScaffoldController;

/**
 * Description of IndexController
 *
 * @author manuel
 */
class IndexController extends ScaffoldController
{
    
    protected function beforeFilter()
    {
        //$this->setTemplate(NULL);
        $this->model = new Usuarios();
    }
}