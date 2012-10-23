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
        $response = new \KumbiaPHP\Kernel\Response();
        $response->cache('+1 min');
        return $this->render($response);
    }

    public function otro()
    {
        return new \KumbiaPHP\Kernel\Response("<html><body>Mi Respuesta...!!!</body></html>");
    }

}