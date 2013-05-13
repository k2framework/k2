<?php

namespace Demos\Seguridad\Controller;

use K2\Form\Form;
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
        $this->form = new Form('form_login');

        $this->form->setAction('_autenticate')
                ->add('username')->setLabel('Nombre de Usuario: ');

        $this->form->add('password', 'password')->setLabel('Contraseña: ');

        if ($this->get('session')->has(Security::LOGIN_ERROR)) {
            $this->form->addError('TODO', App::get('session')->get(Security::LOGIN_ERROR));
            App::get('session')->delete(Security::LOGIN_ERROR);
        }
    }

}
