<?php

namespace Demos\AngularJs\Controller;

use K2\Kernel\Controller\Controller;

class IndexController extends Controller
{

    public function index_action()
    {
        
    }

    public function get_action()
    {
        return new \K2\Kernel\JsonResponse(\Demos\Modelos\Model\Usuarios::findAll('array'));
    }

    public function crear_action()
    {
        if ($this->getRequest()->isMethod('post')) {
            $user = new \Demos\Modelos\Model\Usuarios($this->getRequest()->post('persona'));
            
            $user->save();
            
            return new \K2\Kernel\JsonResponse((array)$user->getErrors());
        }
    }

}
