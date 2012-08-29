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
    public function index()
    {
        
    }

    public function otro()
    {
        return new \KumbiaPHP\Kernel\Response("<html><body>Mi Respuesta</body></html>");
    }

}