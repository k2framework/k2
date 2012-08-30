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
        var_dump($this->get('validator')->validate($this->get('mi_servicio')));
       return $this->render(new \KumbiaPHP\Kernel\Response('',300,array(
           'Content-Type' => 'application/json'
       )));
    }

    public function otro()
    {
        return new \KumbiaPHP\Kernel\Response("<html><body>Mi Respuesta</body></html>");
    }

}