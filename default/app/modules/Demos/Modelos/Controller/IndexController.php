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
        $this->setView(NULL);
    }

    public function index()
    {
        $usr = new Usuarios();
        
        //$usr->create();
        
        var_dump($this->get('validator')->validate($usr));
        var_dump($usr->getErrors());
        
    }

}