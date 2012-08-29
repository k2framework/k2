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

    public function index(\KumbiaPHP\Kernel\Request $request,$param = NULL)
    {                
        //return new \KumbiaPHP\Kernel\Response($request->getRequestUri() . "  <br/> $param");
    }

    public function accion2()
    {
        $this->get('otro_servicio')->mensaje($this->get("flash")->get("error"));
    }

}