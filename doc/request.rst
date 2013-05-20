El Objeto Request
=================

Este objeto es una capa de abstracción orientada a objetos de las variables globales de PHP, es decir, permite obtener valores de $_GET, $_POST, y $_SERVER por medio de una capa ( Clase Request ), orientada a objetos.

.. contents:: Las ventajas de esto son que evitamos tener que realizar tareas comunes que requieren un poco más de trabajo haciendolas a mano.

Como obtengo la instancia del Request actual
--------------------------------------------

En realidad esto es muy sencillo, en cualquier controlador que extienda de Controller, podemos llamar al método "getRequest()" y este nos devolverá la instancia del request actual, ejemplo:

.. code-block:: php

    //archivo app/modules/MiModulo/Controller/usuariosController.php

    namespace MiModulo\\Controller;

    use K2\\Kernel\\Controller\\Controller;

    class usuariosController extends Controller
    {
        public function index_action()
        {
            $request = $this->getRequest(); //este método nos devuelve la instancia del request actual.
            if ( $request->isAjax() ){
                ....
            }
        }
    }

Obteniendo valores de la Peticion
---------------------------------

Generalmente una petición viene acompañada de valores que nos envia el cliente, ya sea por medio de un formulario, de la url, etc. y la aplicación debe recibir y procesar dichos datos de alguna manera dependiendo de la lógica de cada programa.

La clase Request ofrece una serie de métodos para devolvernos esos valores de manera orientada a objetos, supongamos que tenemos un formulario de registro, en el que pedimos el login de la persona y la edad, dicha persona envia el formulario por medió del método POST, nosotros podremos obtener esos datos de la siguiente manera:

.. code-block:: php

    //archivo app/modules/MiModulo/Controller/usuariosController.php

    namespace MiModulo\\Controller;

    use K2\\Kernel\\Controller\\Controller;

    class usuariosController extends Controller
    {
        public function registrar()
        {
            $request = $this->getRequest(); //este método nos devuelve la instancia del request actual.
            $login = $request->request("login"); //obtenemos el login
            $edad = $request->request("edad"); //obtenemos la edad
            .... procesamos el formulario ....
        }
    }

Los métodos request, get, post, y server
______________________________________________________

La clase Request tiene 5 métodos utiles para obtener data de las variables globales de PHP ($_REQUEST, $_GET, $_POST, $_SERVER), estas son:

    * Request->request($key, $default = null): devuelve el valor para un indice contenido en $_REQUEST.
    * Request->get($key): devuelve el valor para un indice contenido en $_GET.
    * Request->server($key, $default = null): devuelve el valor para un indice contenido en $_SERVER de PHP.

Estos métodos, permiten especificar un valor por defecto a devolver, si no se encuentra el indice especificado.

Otros metodos Utiles
====================

Acá estan listados los métodos de la clase Request:

    * getSession(): Devuelve la instancia del manejador de sesiones.
    * getMethod(): Devuelve el metodo de la petición
    * getClientIp(): Devuelve la IP del cliente
    * isAjax(): Devuelve TRUE si la petición es Ajax
    * isMethod($method): Devuelve TRUE si el método de la petición es el pasado por parametro
    * getRequestUrl(): Devuelve la url de la petición actual
    * getContent(): Devuelve el Cuerpo de la petición
    
