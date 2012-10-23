<?php

namespace Index\Controller;

use KumbiaPHP\Kernel\Controller\Controller;

/**
 * Description of IndexController
 *
 * @author manuel
 */
class IndexController extends Controller
{

    protected function beforeFilter()
    {
        if ($this->getRequest()->isAjax()) {
            $this->setTemplate(NULL);
        }
    }

    public function index()
    {
        //$this->cache('+10 sec');
    }

    public function otro()
    {
        return new \KumbiaPHP\Kernel\Response("<html><body>Mi Respuesta...!!!</body></html>");
    }

}