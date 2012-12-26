El Objeto Request
=================

Este objeto es una capa de abstracción orientada a objetos de las variables globales de PHP, es decir, permite obtener valores de $_GET, $_POST, $_FILES, y $_SERVER por medio de una capa ( Clase Request ), orientada a objetos.

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
            $login = $request->request->getAlnum("login"); //obtenemos el login filtrado solo con caracteres alfanumericos
            $edad = $request->request->getInt("edad"); //obtenemos la edad filtrada son con números.
            .... procesamos el formulario ....
        }
    }

Las variables request, query, files, cookies, y server
______________________________________________________

La clase Request tiene 5 atributos publicos, los cuales son el equivalente a las variables globales de PHP, estas son:

    * Request->request: representa a la variable $_POST de PHP y a las variables PUT y DELETE, que no existen en PHP.
    * Request->query: representa a la variable $_GET de PHP.
    * Request->files: representa a la variable $_FILES de PHP.
    * Request->cookies: representa a la variable $_COOKIES de PHP.
    * Request->server: representa a la variable $_SERVER de PHP.

Estos atributos públicos, no solo son arreglos de datos como sus equivalentes en PHP, sino que son objetos con métodos para establecer y leer los datos que contienen.

El metodo get()
______________

::

    public function get($key, $default = NULL)

El objeto Request ofrece un método llamado get($key, $default = NULL) el cual espera un indice y un valor por defecto si no se encuentra el valor buscado en el indice.

Este método sirve para obtener un dato que esté en el atributo request, ó en el atributo query, ó en cookies, Siendo esta mismo el orden de busqueda, es decir, que primero verifica la existencia del $key en la propiedad public $request de la clase Request, y si no encuentra esa clave, busca en el atributo public $query, de no encontrar la clave acá tampoco, busca en $cookies, y si no existe en ninguno de los tres atributos, retorna el valor por defecto pasado como segundo parametro del método get.

Ejemplo de uso:

.. code-block:: php

    //archivo app/modules/MiModulo/Controller/usuariosController.php

    namespace MiModulo\\Controller;

    use K2\\Kernel\\Controller\\Controller;

    class usuariosController extends Controller
    {
        public function registrar_action()
        {
            $busqueda = $this->getRequest()->get("q", "todos");
            //el método get, buscará en $request y si no existe, buscará en $query, 
            //y si acá tampoco existe, lo hará en $cookies. Por ultimo, sino está en ningun lado, devuelve "todos"
        }
    }

Otros metodos Utiles
====================

Acá estan listados los métodos de la clase Request:

    * get($key, $default = NULL): Devuelve el valor para un indice de las variables globales de la petición
    * getSession(): Devuelve la instancia del manejador de sesiones.
    * getAppContext(): Devuelve la instancia del objeto que tiene el contexto de la aplicación
    * getMethod(): Devuelve el metodo de la petición
    * getClientIp(): Devuelve la IP del cliente
    * isAjax(): Devuelve TRUE si la petición es Ajax
    * isMethod($method): Devuelve TRUE si el método de la petición es el pasado por parametro
    * getBaseUrl(): Devuelve el url base del proyecto
    * getRequestUrl(): Devuelve la url de la petición actual
    * getContent(): Devuelve el Cuerpo de la petición
    