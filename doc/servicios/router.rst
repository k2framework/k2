El Servicio Router
==================

El servicio KumbiaPHP\Kernel\Router\Router ofrece la posibilidad de redirigir la petición actual a otra petición.

.. contents:: Esta clase lo que hace es devolver un objeto Response con las cabeceras http necesarias para redirir una petición a otra ubicación.

Metodos Disponibles
-------------------

redirect()
_________
.. code-block:: php

    /**
     * Redirije la petición a otro modulo/controlador/accion de la aplicación.
     * @param string $url
     * @return \KumbiaPHP\Kernel\RedirectResponse 
     */
    public function redirect($url = NULL, $status = 302)

toAction()
_________
.. code-block:: php

    /**
     * Redirije la petición a otra acción del mismo controlador.
     * @param type $action
     * @return \KumbiaPHP\Kernel\RedirectResponse 
     */
    public function toAction($action = NULL, $status = 302)

forward()
________
.. code-block:: php

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
Ejemplo Basico
______________
.. code-block:: php

    <?php

    namespace MiModulo\Controller;

    use KumbiaPHP\Kernel\Controller\Controller;

    class usuariosController extends Controller
    {
        public function index_action()
        {
            
        }

        public function listado_action()
        {
            return $this->getRouter()->redirect("NombreModulo:usuarios/index");//redirije a la accion index()
            return $this->getRouter()->redirect("NombreModulo:usuarios");//redirije a la accion index()
            return $this->getRouter()->toAction();//redirije a la accion index()
            return $this->getRouter()->toAction("index");//redirije a la accion index()
        }

        public function todos()
        {
            //tambien podemos llamar al servicio usado el método get() del controlador
            return $this->get("router")->forward("NombreModulo:usuarios/index");redireccion interna hacia index()
            return $this->get("router")->forward("OtroModulo:compras");redireccion interna hacia index()
            return $this->getRouter()->forward("OtroModulo:compras");redireccion interna hacia index()
        }
    }

El return es OBLIGATORIO, ya que debemos retornar el objeto Response creado por los métodos del servicio @router, de no hacerlo, no se hará la redirección

Ejemplo Avanzado
________________

Se enviará un correo a travez de un servicio ficticio llamado @mail, el correo es una vista/template de la aplicación, que da la bienvenida a un usuario recien registrado.

.. code-block:: php

    <?php

    namespace Registro\Controller;

    use KumbiaPHP\Kernel\Controller\Controller;

    class registroController extends Controller
    {
        public function enviar_correo_action($usuarioId)
        {
            //obtenemos el contenido de la url email_templates/usuarios/registro/{id}
            //el cual es el html que se enviará por correo.

            $response = $this->getRouter()->forward("K2/EmailTemplates:/usuarios/registro/$usuarioId");

            if ( 200 === $response->getStatus() ){ //si la respuesta es exitosa.
                $email = $this->get("mail")
                                    ->setSubject("Registro Exitoso")
                                    ->setContent($response->getContet());
                if ( $email->send() ){
                    $this->get("flash")->success("El correo fué enviado con éxito...!!!");
                }else{ //si hubo un error.
                    $this->get("flash")->error("No se Pudo enviar el Correo...!!!");
                }
            }else{ //si hubo un error.
                $this->get("flash")->error("No se Pudo enviar el Correo...!!!");
            }
        }
    }

Como se puede ver, este es un ejemplo avanzado del uso del router, se usa el método forward para obtener la respuesta de otra petición, este método devuelve un objeto Response, a travez del cual podemos verficar el status de la respuesta y el contenido html que nos devolvió.

Luego de obtener y validar la respuesta, usamos el servicio @mail para enviar el correo.