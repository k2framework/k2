<?php

namespace Rest\Controller;

//use KumbiaPHP\Kernel\Controller\Controller;
use KumbiaPHP\Kernel\Request;
use KumbiaPHP\Kernel\Response;

/**
 * Description of IndexController
 *
 * @author maguirre
 */
class IndexController //extends Controller
{

    protected function beforeFilter(Request $request)
    {
        return strtolower($request->getMethod());
    }

    public function get()
    {
        $data = array(
            'variable' => "Hola Mundo REST",
            'metodo' => "GET",
        );
        
        return new Response(json_encode($data), 200, array('Content-Type' => 'application/json'));
    }

    public function post()
    {
        $data = array(
            'variable' => "Hola Mundo REST",
            'metodo' => "POST",
        );
        return new Response(json_encode($data), 200, array('Content-Type' => 'application/json'));
    }

    public function put()
    {
        $data = array(
            'variable' => "Hola Mundo REST",
            'metodo' => "PUT",
        );
        return new Response(json_encode($data), 200, array('Content-Type' => 'application/json'));
    }

    public function delete()
    {
        $data = array(
            'variable' => "Hola Mundo REST",
            'metodo' => "DELETE",
        );
        return new Response(json_encode($data), 200, array('Content-Type' => 'application/json'));
    }

}
