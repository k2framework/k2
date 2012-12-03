<?php

namespace Demos\Seguridad\Controller;

use KumbiaPHP\Form\Form;
use KumbiaPHP\Security\Security;
use KumbiaPHP\Kernel\Controller\Controller;

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
        $this->usuario = $this->get('security')->getToken()->getUser();
    }

    public function login_action()
    {
        $this->form = new Form('form_login');

        $this->form->setAction('_autenticate')
                ->add('username')->setLabel('Nombre de Usuario: ');

        $this->form->add('password', 'password')->setLabel('Contraseña: ');

        if ($this->get('session')->has(Security::LOGIN_ERROR)) {
            $this->form->addError('TODO', $this->get('session')->get(Security::LOGIN_ERROR));
            $this->get('session')->delete(Security::LOGIN_ERROR);
        }
    }

}
