<?php

namespace Demos\Seguridad\Controller;

use KumbiaPHP\Kernel\Controller\Controller;
use KumbiaPHP\Kernel\Response;

/**
 * Ejemplo de un controlador REST FULL
 * 
 * Este controlador puede manejar peticiones de tipo rest
 *
 * @author maguirre
 */
class IndexController extends Controller
{

    public function index()
    {
        if ($this->get('security')->isLogged()) {
            var_dump("Logueado", $this->get('security')->getToken()->getUser());
        }

        if ($this->get('security')->isLogged('usuario_comun')) {
            echo "SI";
        } else {
            echo "NO";
        }
    }

    public function login()
    {
        $this->form = new \KumbiaPHP\Form\Form('form_login');

        $this->form->setAction('_autenticate')
                ->add('login')->setLabel('Nombre de Usuario: ');

        $this->form->add('clave', 'password')->setLabel('Contraseña: ');
    }

}
