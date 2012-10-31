El Servicio Router
==================

El servicio KumbiaPHP\Kernel\Router\Router ofrece la posibilidad de redirigir la petición actual a otra petición.

.. contents:: Esta clase lo que hace es devolver un objeto Response con las cabeceras http necesarias para redirir una petición a otra ubicación.

Métodos Disponibles
-------------------

redirect()
_________
::

    /**
     * Redirije la petición a otro modulo/controlador/accion de la aplicación.
     * @param string $url
     * @return \KumbiaPHP\Kernel\RedirectResponse 
     */
    public function redirect($url = NULL, $status = 302)

toAction()
_________
::

    /**
     * Redirije la petición a otra acción del mismo controlador.
     * @param type $action
     * @return \KumbiaPHP\Kernel\RedirectResponse 
     */
    public function toAction($action = NULL, $status = 302)

forward()
________
::

    /**
     * Redirije la petición a otro modulo/controlador/accion de la aplicación internamente,
     * es decir, la url del navegador no va a cambiar para el usuario.
     * @param type $url
     * @return type
     * @throws \LogicException 
     */
    public function forward($url)

Ejemplo de Uso
--------------
Ejemplo Básico
______________
::

    <?php

    namespace MiModulo\Controller;

    use KumbiaPHP\Kernel\Controller\Controller;

    class UsuariosController extends Controller
    {
        public function index()
        {
            
        }

        public function listado()
        {
            return $this->getRouter()->redirect("nombre_modulo/usuarios/index");//redirije a la accion index()
            return $this->getRouter()->redirect("nombre_modulo/usuarios");//redirije a la accion index()
            return $this->getRouter()->toAction();//redirije a la accion index()
            return $this->getRouter()->toAction("index");//redirije a la accion index()
        }

        public function todos()
        {
            //tambien podemos llamar al servicio usado el método get() del controlador
            return $this->get("router")->forward("nombre_modulo/usuarios/index");redireccion interna hacia index()
            return $this->get("router")->forward("otro_modulo/compras");redireccion interna hacia index()
            return $this->getRouter()->forward("otro_modulo/compras");redireccion interna hacia index()
        }
    }

El return es Obligatorio, ya que debemos retornar el objeto Response creado por los métodos del servicio @router, de no hacerlo, no se hará la redirección.