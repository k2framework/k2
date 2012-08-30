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
        $this->get('mi_servicio')->show("Hola Mundo...!!!");
    }

    public function otro()
    {
        return new \KumbiaPHP\Kernel\Response("<html><body>Mi Respuesta</body></html>");
    }

}