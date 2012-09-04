La Clase Response
=================

Esta clase es la encargada de mostrar el contenido que se devuelva en la petición, es decir, todo lo que queramos mostrar ó devolver como resultado de la ejecución de una patetición, debemos hacerlo a traves de la clase Response.

Ejemplo:
-------

::

    //archivo app/modules/MiModulo/Controller/UsuariosController.php
    <?php

    namespace MiModulo\\Controller;

    use KumbiaPHP\\Kernel\\Controller\\Controller;
    use KumbiaPHP\\Kernel\\Response;

    class UsuariosController extends Controller
    {
        public function index()
        {
            return new Response("<html><body>Hola, Esta es la Respuesta en HTML</body></html>");
        }
    }

En este ejemplo, el método index del controlador UsuariosController, devuelve un objeto response con un contenido html (la respuesta puede ser de cualquier tipo, html, json, xml, csv, pdf, xls, etc.), que el fw usará para devolverla al cliente. Realmente no es obligatorio retornar una respuesta en cada acción de un controlador, ya que el kernel verifica si hemos ó no devuelto una instancia de Response, y si no lo hicimos crea la respuesta a traves del servicio @view, que es quien se encargará de buscar la vista y el template para cada petición.

Parametros de la Respuesta
--------------------------

El constructor de la clase Response puede recibir tres parametros, el primero es un string con el contenido de la respuesta, el segundo (opcional) es un numero que representa el status de la respuesta (por defecto 200), y el ultimo argumento es un array con las cabeceras a usar para la respuesta(Content-Type , Location, etc).

Ejemplo:

::

    //archivo app/modules/MiModulo/Controller/UsuariosController.php
    <?php

    namespace MiModulo\\Controller;

    use KumbiaPHP\\Kernel\\Controller\\Controller;
    use KumbiaPHP\\Kernel\\Response;

    class UsuariosController extends Controller
    {
        public function index()
        {
            $content = "<html><body>Hola, Esta es la Respuesta en HTML</body></html>";
            return new Response($content, 200, array( "Content-Type" => "text/html" ));
        }

        public function json()
        {
            $content = array("nombres" => "Manuel José", "edad" => "23");
            return new Response(json_encode($content), 200, array( "Content-Type" => "application/json" ));
        }

        public function redirect()
        {
            return new Response(NULL, 203, array( "Location" => "www.google.com" ));
        }
    }

Esos son algunos ejemplos de uso de la clase Response, aunque en la mayoria de los casos no seremos nosotros quienes devolvamos la respuesta, sino que el servicio @view será quien haga este trabajo por nosotros de manera transparente.