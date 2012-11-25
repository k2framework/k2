El Controlador
==============

Los controladores en KumbiaPHP 2 son muy parecidos a los controladores de la versión 1 del framework, estan compuestos de métodos que si concuerdan con el patrón de la url de una petición, el kernel del framework los invoca y le pasa los parametros que estos soliciten.

.. contents:: Ademas se siguen teniendo los filtros pre y post ejecución de la acción correspondiente.

Nombre de la Clase
------------------

En esta versión del framework, tanto los nombres de clases como nombres de archivos se escriben exactamente igual. El nombre del controlador preferiblemente en **small_case**, y debe ir seguido del Sufijo **Controller** obligatoriamente.

Ejemplo de un Controlador
_________________________

.. code-block:: php

    //archivo app/modules/MiModulo/Controller/usuariosController.php

    namespace MiModulo\Controller;

    use KumbiaPHP\Kernel\Controller\Controller;

    class usuariosController extends Controller //ahora se extiende de una clase base Controller.
    {
        public function index_action()
        {
            $this->mensaje = "Hola Mundo...!!!";
        }
    }

Este es un ejemplo de un controlador llamado usuariosController, el cual extiende de la clase base Controller, y tiene un método llamado index_action() que crea una variable "mensaje" con el valor "Hola Mundo...!!!".

Como debe ser la Ruta para acceder a un Controlador
___________________________________________________

La ruta que identifica a un controlador debe ser el nombre exacto del controlador pero sin el sufijo Controller, veamos algunos ejemplos:

::

    Supongamos que estamos en el Módulo Usuarios:

    Modulo   / controlador / acción             =>      Controlador a ejecutar:

    /usuarios                                   =>        indexController
    /usuarios/index                             =>        indexController
    /usuarios/index/index                       =>        indexController
    /usuarios/admin/index                       =>        adminController
    /usuarios/nuevos_ingresos/                  =>        nuevos_ingresosController
    /usuarios/nuevos_ingresos/index             =>        nuevos_ingresosController

Como se puede apreciar las rutas son exactamente iguales a los nombres de los controladores, pero sin el sufijo Controller.

Las Acciónes
------------

Una acción es un método de la clase controladora que puede ser ejecutada por el framework, si se cumplen ciertas condiciones en la url de la petición.

Cabe destacar que las acciónes para poder ser accedidas desde la Url, deben ser métodos públicos.

Nombres para las acciones
_________________________

Los nombres de las acciones puede ser cualquier nombre seguido del sufijo _action, ejemplos:

    * index_action()
    * crear_action()
    * Hola_action()
    * validar_url_action()
    * ...

Como debe ser la Ruta para acceder a una Acción
___________________________________________________

La ruta que identifica a una acción debe ser el nombre exacto de la acción pero sin el sufijo _action, veamos algunos ejemplos:

::

    Supongamos que estamos en el Módulo Usuarios, controlador indexController:

    Modulo   / controlador / acción             =>      Controlador a ejecutar:   =>    Acción a ejecutar

    /usuarios                                   =>        indexController         =>         index_action()
    /usuarios/index                             =>        indexController         =>         index_action()
    /usuarios/index/index                       =>        indexController         =>         index_action()
    /usuarios/index/crear                       =>        indexController         =>         crear_action()
    /usuarios/index/nuevo_ingreso               =>        indexController         =>         nuevo_ingreso_action()
    /usuarios/index/modificar_perfil            =>        indexController         =>         modificar_perfil_action()

Como se puede apreciar las rutas son exactamente iguales a los nombres de las acciones, pero sin el sufijo _action.

Los Filtros
-----------

Los filtros en los controladores son métodos protegidos ó privados que se ejecutan antes y/o despues de la ejecución de la acción del controlador.

Son útiles para verificar que se cumplan ciertas condiciones para ejecutar la acción, ó realizar tareas que son comunes en un controlador y que no queremos repetir en cada acción del mismo.

beforeFilter
____________

El método beforeFilter() es una función que puede tener una clase controladora y que, de existir, el framework llamará y ejecutará justo antes de realizar el llamado y ejecución de la acción solicitada en la petición.

Este método ofrece la posibilidad de cambiar ó evitar la ejecución de una acción, esto se logra devolviendo una cadena con el nombre de la nueva acción a ejecutar ( en el caso de que queramos cambiar la ejecución de la acción actual por otra ), ó devolviendo **false** si no queremos que se ejecute la acción del controlador.

Tambien es posible devolver una instancia de Response, con lo que no se ejecutarán ni la acción ni el afterFilter, sino que se usará esa respuesta para devolverla en la petición.

afterFilter
___________

El método afterFilter() es una función que puede tener una clase controladora y que, de existir, el framework llamará y ejecutará justo despues de realizar el llamado y ejecución de la acción solicitada en la petición.

NOTA: si el método beforeFilter() devuelve false ó una instancia de Response, este filtró no será ejecutado por el kernel del framework.

Parametros de las Acciones
--------------------------

Una acción de un controlador puede tener parametros ó argumentos que esperan ciertos datos de una petición, un ejemplo de esto es el ID de un registro que queremos editar en un CRUD. el framework obtiene los valores para estos argumentos a traves de la URL, donde cada valor pasado por la url despues del nombre de la acción es un parametro de la misma, estos valores deben ir separados por un / unos de otros, veamos unos ejemplos:

.. code-block:: php

    <?php  //controlador app/modules/Home/Controller/usuariosController.php

    namespaces Home\Controller;

    use KumbiaPHP\Kernel\Controller\Controller;

    class usuariosController extends Controller
    {
        //   Ejemplos de url:
        //  /home/usuarios/editar/5   válida
        //  /home/usuarios/editar/10  válida
        //  /home/usuarios/editar/    invalida, el método espera el parametro id, por lo que se lanzará una excepcion
        public function editar_action($id){ //nuestra acción editar recibira en el parametro $id el valor 5
            ...
        }

        //   Ejemplos de url:
        //  /home/usuarios/fecha/10-10-2012   válida
        //  /home/usuarios/fecha/20-10-2012   válida
        //  /home/usuarios/fecha/             válida, si no se pasa el parametro, el mismo toma el valor por defecto.
        public function fecha_action($fecha = 'now'){ //nuestra acción espera el parametro fecha, si no lo recibe toma "now"
            $filtro = new DateTime($fecha); 
            ...
        }

        //   Ejemplos de url:
        //  /home/usuarios/filtrar_entre/03-05-2012/20-12-2012   válida
        //  /home/usuarios/filtrar_entre/20-10-2012/10-08-2012   válida
        //  /home/usuarios/filtrar_entre/                        invalida
        public function filtrar_entre_action($fechaInico, $fechaFinal){
            ...
        }
    }

La clase base Controller
-------------------------

Todos los controladores de la aplicación deben extender de la clase base "KumbiaPHP\Kernel\Controller\Controller", si no lo hacen, el framework lanzará una excepción indicandonos que debemos extender de dicha clase.

Esta clase ofrece ciertos métodos de gran utilidad para ser usados por los controladores de la aplicación, a continuación se detallarán cada uno de ellos:

get()
________

:: 

    Controller->get($id)

Este método devuelve la instancia del servicio, para ser usado en las acciónes del controlador. por ejemplo:

.. code-block:: php

    //archivo app/modules/MiModulo/Controller/usuariosController.php

    namespace MiModulo\Controller;

    use KumbiaPHP\Kernel\Controller\Controller;

    class usuariosController extends Controller //ahora se extiende de una clase base Controller.
    {
        public function index_action()
        {
            echo "Método de la petición: " $this->get("request")->getMethod();
        }
    }    

Con $this->get("request") obtenemos la instancia del objeto request, y luego llamamos al método getMethod() de dicho objeto. Tambien se puede guardar la instancia del objeto en una variable y luego usar los métodos del objeto.

getRequest()
___________

:: 

    Controller->getRequest()

Este método nos devuelve la instancia del objeto request, es una manera más sencilla de hacer $this->get("request") y ademas nos brinda la posibilidad de ver los métodos disponibles al utilizar un IDE que lea la PhpDoc.

getRouter()
__________

:: 

    Controller->getRouter()

Este método nos devuelve la instancia del objeto router, es una manera más sencilla de hacer $this->get("router") y ademas nos brinda la posibilidad de ver los métodos disponibles al utilizar un IDE que lea la PhpDoc.

getView()
_________

:: 

    Controller->getView()

Este método nos devuelve una cadena que representa el nombre de la vista a renderizar por el servicio @view.

setView()
________

:: 

    Controller->setView($view, $template = FALSE)

Este método permite establecer la vista que el servicio @view deberá renderizar. Ademas podemos establecer de 
una vez el template a usar. Tambien es posible dejar de mostrar la vista y/ó el template pasando null en los parametros.

.. code-block:: php

    //archivo app/modules/MiModulo/Controller/usuariosController.php

    namespace MiModulo\Controller;

    use KumbiaPHP\Kernel\Controller\Controller;

    class usuariosController extends Controller //ahora se extiende de una clase base Controller.
    {
        public function index_action()
        {
            $this->setView("listado"); //va a renderizar la vista listado.phtml
            $this->setView(null); //no se va a renderizar ninguna vista solo el template.
            $this->setView("listado",null); //va a renderizar la vista listado.phtml sin template
            $this->setView(null,null); //no se mostrará ni vista ni template
            $this->setView("listado","otro_template"); //vista listado.phtml y template otro_template.phtml
        }
    }  

getTemplate()
____________

:: 

    Controller->getTemplate()

Este método nos devuelve una cadena que representa el nombre del template a renderizar por el servicio @view.

setTemplate()
____________

:: 

    Controller->setTemplate($template)

Este método permite establecer el template que el servicio @view deberá renderizar. Tambien es posible pasar
null para indicar que no queremos que se muestre el template. 

Los templates se pueden clasificar en dos grupos:

    * Templates Publicos : Se encuentran en "proyecto/app/view/templates/"
    * Templates de Módulos: Se encuentran en la carpeta "view/_shared/templates/" de cada módulo de la app.

.. code-block:: php

    //archivo app/modules/MiModulo/Controller/usuariosController.php

    namespace MiModulo\Controller;

    use KumbiaPHP\Kernel\Controller\Controller;

    class usuariosController extends Controller //ahora se extiende de una clase base Controller.
    {
        public function index_action()
        {
            $this->setTemplate("admin"); //va a renderizar el template publico admin.phtml
            $this->setTemplate("MiModulo:admin"); //va a renderizar el template admin.phtml del módulo "MiModulo"
            $this->setTemplate("K2/Backend:default");//renderiza el template default.phtml del módulo "K2/Backend"
        }
    } 

Cuando queremos utilizar un template de un módulo y no uno público, debemos especificar el nombre del módulo seguido del simbolo de dos puntos ":" y luego el nombre del template, por ejemplo:

    * **K2/Backend:default** -> el módulo es K2/Backend y el template es default.phtml
    * **K2/EmailTemplate:default** -> el módulo es K2/EmailTemplate y el template es default.phtml
    * **Twitter:default** -> el módulo es K2/Backend y el template es default.phtml

El nombre del módulo es el namespace del módulo hasta la carpeta donde se encuentren los controladores, módelos, vistas y demas archivos del mismo.

cache()
______

:: 

    Controller->cache($time = FALSE)

Establece el tiempo de caché para una vista ó controlador completos, se debe pasar un `intervalo de tiempo válido <http://www.php.net/manual/es/datetime.formats.relative.php>`_, si se pasa false, no se cachea. Por ejemplo:

.. code-block:: php

    //archivo app/modules/MiModulo/Controller/usuariosController.php

    namespace MiModulo\Controller;

    use KumbiaPHP\Kernel\Controller\Controller;

    class usuariosController extends Controller //ahora se extiende de una clase base Controller.
    {
        protected function beforeFilter()
        {
            $this->cache('+10 min'); //se cachean todas las respuestas del controlador por 10 minutos.
        }

        public function index_action()
        {
            $this->cache('+1 min'); //se cachea la respuesta por 1 minuto
            $this->cache('+10 hour'); //se cachea la respuesta por 10 horas
            $this->cache(false); //deja de cachear la respuesta
        }
    } 

Cabe destacar que la cache solo se activa en produccíon y si se usa la clase AppCache() en public/index.php

render()
_______

:: 

    Controller->render(array $params = array(), $time = NULL)

LLama al servicio @view y nos devuelve la respuesta ya construida con el template y view especificados. Se le pueden pasar parametros que serán las variables en la vista y un tiempo de cache.

Este método es util cuando queremos enviar la respuesta por correo por ejemplo. crear un PDF, etc.

renderNotFound()
_______________

:: 

    Controller->renderNotFound($message)

Este método lanza una excepcion NotFoundException, podemos mostrar un mensaje para verlo en el entorno de desarrollo, en producción se mostrará la vista 404.phtml de "app/views/errors/"


