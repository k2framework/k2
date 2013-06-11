<?php

namespace Demos\AngularJs\Controller;

use K2\Kernel\JsonResponse;
use Demos\Modelos\Model\Usuarios;
use K2\Kernel\Controller\Controller;

class PersonasController extends Controller
{

    public function beforeFilter()
    {
        return strtolower($this->getRequest()->getMethod()) . '_action';
    }

    public function get_action()
    {
        if ($id = $this->getRequest()->get('id', false)) {
            return new JsonResponse(Usuarios::findById($id, true, 'array'));
        } else {
            return new JsonResponse(Usuarios::findAll('array'));
        }
    }

    public function post_action()
    {
        if ($this->getRequest()->isMethod('post')) {
            
            $user = new Usuarios($this->getRequest()->request('persona'));

            $user->save();

            return new JsonResponse(array(
                'persona' => $user,
                'errors' => (array) $user->getErrors()
            ));
        }
    }

}
