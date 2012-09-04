El Objeto Request
=================

Este objeto es una capa de abstracción orientada a objetos de las variables globales de PHP, es decir, permite obtener valores de $_GET, $_POST, $_FILES, y $_SERVER por medio de una capa ( Clase Request ), orientada a objetos.

Las ventajas de esto son que evitamos tener que realizar tareas comunes que requieren un poco más de trabajo haciendolas a mano.

Como obtengo la instancia del Request actual
--------------------------------------------

En realidad esto es muy sencillo, en cualquier controlador que extienda de Controller, podemos llamar al método "getRequest()" y este nos devolverá la instancia del request actual, ejemplo:

::

    //archivo app/modules/MiModulo/Controller/UsuariosController.php
    <?php

    namespace MiModulo\\Controller;

    use KumbiaPHP\\Kernel\\Controller\\Controller;

    class UsuariosController extends Controller
    {
        public function index()
        {
            $request = $this->getRequest(); //este método nos devuelve la instancia del request actual.
            if ( $request->isAjax() ){
                ....
            }
        }
    }

Ahora, si tenemos un controlador que no extiende de Controller, y queremos obtener la instancia de Request, debemos tener como primer parametro en nuestra acción lo siguiente:

::

    //archivo app/modules/MiModulo/Controller/UsuariosController.php
    <?php

    namespace MiModulo\\Controller;

    class UsuariosController
    {
        public function index(Request $request)
        {
            //el framework al llamar a la acción index, le pasará la instancia de $request al parametro.
            $method = $request->getMethod(); 
        }
    }

Obteniendo valores de la Petición
---------------------------------

Generalmente una petición viene acompañada de valores que nos envia el cliente, ya sea por medio de un formulario, de la url, etc. y la aplicación debe recibir y procesar dichos datos de alguna manera dependiendo de la lógica de cada programa.

La clase Request ofrece una serie de métodos para devolvernos esos valores de manera orientada a objetos, supongamos que tenemos un formulario de registro, en el que pedimos el login de la persona y la edad, dicha persona envia el formulario por medió del método POST, nosotros podremos obtener esos datos de la siguiente manera:

::

    //archivo app/modules/MiModulo/Controller/UsuariosController.php
    <?php

    namespace MiModulo\\Controller;

    use KumbiaPHP\\Kernel\\Controller\\Controller;

    class UsuariosController extends Controller
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

El método get($key, $default = NULL)
___________________________________