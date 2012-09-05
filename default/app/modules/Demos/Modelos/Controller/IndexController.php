<?php

namespace Demos\Modelos\Controller;

use KumbiaPHP\Kernel\Controller\Controller;
use Demos\Modelos\Model\Usuarios;

/**
 * Description of IndexController
 *
 * @author manuel
 */
class IndexController extends Controller
{
    
    protected function beforeFilter()
    {
        $this->setTemplate(NULL);
    }

    public function index()
    {
        $usr = new Usuarios();
        
        $this->usuarios = $usr->findAll();
        
    }

}