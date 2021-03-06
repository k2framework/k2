El Servicio Router
==================

El servicio K2\Kernel\Router\Router ofrece la posibilidad de redirigir la petición actual a otra petición.

.. contents:: Esta clase lo que hace es devolver un objeto Response con las cabeceras http necesarias para redirir una petición a otra ubicación.

Metodos Disponibles
-------------------

redirect()
_________
.. code-block:: php

    /**
     * Redirije la petición a otro modulo/controlador/accion de la aplicación.
     * @param string $url
     * @return \K2\Kernel\RedirectResponse 
     */
    public function redirect($url = null, $status = 302)

toAction()
_________
.. code-block:: php

    /**
     * Redirije la petición a otra acción del mismo controlador.
     * @param type $action
     * @return \K2\Kernel\RedirectResponse 
     */
    public function toAction($action = null, $status = 302)

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

createUrl()
________
.. code-block:: php

    /**
     * Crea una url válida dentro de la app. todos las libs y helpers la usan.
     * 
     * Ejemplos:
     * 
     * $this->createUrl('admin/usuarios/perfil');
     * $this->createUrl('admin/roles');
     * $this->createUrl('admin/recursos/editar/2');
     * $this->createUrl('@K2Backend/usuarios'); módulo:controlador/accion/params
     * 
     * El ultimo ejemplo es una forma especial de crear rutas
     * donde especificamos el nombre del módulo en vez del prefijo.
     * ya que el prefijo lo podemos cambiar a nuestro antojo.
     * 
     * @param string $url
     * @param boolean $baseUrl indica si se devuelve con el baseUrl delante ó no
     * @return string
     * @throws NotFoundException si no existe el módulo
     */
    public function createUrl($url, $baseUrl = true)

Ejemplo de Uso
--------------
Ejemplo Basico
______________
.. code-block:: php

    <?php

    namespace MiModulo\Controller;

    use K2\Kernel\App;
    use K2\Kernel\Controller\Controller;

    class usuariosController extends Controller
    {
        public function index_action()
        {
            
        }

        public function listado_action()
        {
            return $this->getRouter()->redirect("@NombreModulo/usuarios/index");// redirige al modulo NombreModule controlador usuarios acción index
            return $this->getRouter()->redirect("@NombreModulo/usuarios");//lo mismo que el anterior
            
            return $this->getRouter()->toAction("index");//lo mismo que el anterior
            return $this->getRouter()->toAction();//redirije a la accion index()
        }

        public function todos()
        {
            //tambien podemos llamar al servicio usado el método get() del controlador
            return App::get("router")->forward("NombreModulo:usuarios/index");redireccion interna hacia index()
            return App::get("router")->forward("OtroModulo:compras");redireccion interna hacia index()
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

    use K2\Kernel\App;
    use K2\Kernel\Controller\Controller;

    class registroController extends Controller
    {
        public function enviar_correo_action($usuarioId)
        {
            //obtenemos el contenido de la url email_templates/usuarios/registro/{id}
            //el cual es el html que se enviará por correo.

            $response = $this->getRouter()->forward("@K2EmailTemplates/usuarios/registro/$usuarioId");

            if ( 200 === $response->getStatus() ){ //si la respuesta es exitosa.
                $email = App::get("mail")
                                    ->setSubject("Registro Exitoso")
                                    ->setContent($response->getContet());
                if ( $email->send() ){
                    App::get("flash")->success("El correo fué enviado con éxito...!!!");
                }else{ //si hubo un error.
                    App::get("flash")->error("No se Pudo enviar el Correo...!!!");
                }
            }else{ //si hubo un error.
                App::get("flash")->error("No se Pudo enviar el Correo...!!!");
            }
        }
    }

Como se puede ver, este es un ejemplo avanzado del uso del router, se usa el método forward para obtener la respuesta de otra petición, este método devuelve un objeto Response, a travez del cual podemos verficar el status de la respuesta y el contenido html que nos devolvió.

Luego de obtener y validar la respuesta, usamos el servicio @mail para enviar el correo.
