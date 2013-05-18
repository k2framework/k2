<?php

namespace Demos\Seguridad\Controller;

use K2\Kernel\App;
use K2\Security\Security;
use K2\Kernel\Controller\Controller;

/**
 * Ejemplo de un controlador REST FULL
 * 
 * Este controlador puede manejar peticiones de tipo rest
 *
 * @author maguirre
 */
class indexController extends Controller
{

    public function index_action()
    {
        $this->usuario = App::getUser();
    }

    public function login_action()
    {

        if (App::get('session')->has(Security::LOGIN_ERROR)) {
            App::get('flash')->error(App::get('session')->get(Security::LOGIN_ERROR));
            App::get('session')->delete(Security::LOGIN_ERROR);
        }
    }

}
