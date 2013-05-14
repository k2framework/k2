<?php

namespace Index\Controller;

use K2\Kernel\Controller\Controller;

/**
 * Description of IndexController
 *
 * @author manuel
 */
class indexController extends Controller
{

    protected function beforeFilter()
    {
        if ($this->getRequest()->isAjax()) {
            $this->setTemplate(NULL);
        }
    }

    public function index_action()
    {
        $form = $this->createForm(new \Index\Form\TestForm());

        if ($this->getRequest()->isMethod('POST')) {
            var_dump($this->getRequest()->request($form->getName()));
            $form->bind($this->getRequest());
        }

        $this->form = $form->createView();
    }

    public function otro_action()
    {
        return new \K2\Kernel\Response("<html><body>Mi Respuesta...!!!</body></html>");
    }

}