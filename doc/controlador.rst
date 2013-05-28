El Controlador
==============

Los controladores en K2 estan compuestos de métodos que si concuerdan con el patrón de la url de una petición (y tengan el sufijo _action), el kernel del framework los invoca y le pasa los parametros que estos soliciten.

.. contents:: Ademas se tienen los filtros pre y post ejecución de la acción correspondiente.

Nombre de la Clase
------------------

Tanto los nombres de clases como nombres de archivos se escriben exactamente igual. El nombre del controlador preferiblemente en **small_case**, y debe ir seguido del Sufijo **Controller** obligatoriamente.

Ejemplo de un Controlador
_________________________

.. code-block:: php

    //archivo app/modules/MiModulo/Controller/usuariosController.php

    namespace MiModulo\Controller;

    use K2\Kernel\Controller\Controller;

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

    use K2\Kernel\Controller\Controller;

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

Todos los controladores de la aplicación deben extender de la clase base "K2\Kernel\Controller\Controller", si no lo hacen, el framework lanzará una excepción indicandonos que debemos extender de dicha clase.

Esta clase ofrece ciertos métodos de gran utilidad para ser usados por los controladores de la aplicación, a continuación se detallarán cada uno de ellos:

getRequest()
___________

:: 

    Controller->getRequest()

Este método nos devuelve la instancia del objeto request.

getRouter()
__________

:: 

    Controller->getRouter()

Este método nos devuelve la instancia del objeto router.

getView()
_________

:: 

    Controller->getView()

Este método nos devuelve una cadena que representa el nombre de la vista a renderizar por el servicio @view.

setView()
________

:: 

    Controller->setView($view)

Este método permite establecer la vista que el servicio @view deberá renderizar. Tambien es posible dejar de mostrar la vista pasando false en los parametros.

.. code-block:: php

    //archivo app/modules/MiModulo/Controller/usuariosController.php

    namespace MiModulo\Controller;

    use K2\Kernel\Controller\Controller;

    class usuariosController extends Controller //ahora se extiende de una clase base Controller.
    {
        public function index_action()
        {
            $this->setView("listado"); //va a renderizar la vista proyecto/app/view/listado.twig
            $this->setView(false); //no se va a renderizar ninguna vista.
            
            $this->setView("@K2Backend/reportes/nuevos_ingresos");
            //va a renderizar la vista CarpetaModuloK2Backend/View/reportes/nuevos_ingresos.twig
            
            $this->setView("@K2Backend/ajax");
            //va a renderizar la vista CarpetaModuloK2Backend/View/ajax.twig
        }
    }  


Cuando queremos utilizar una vista de un módulo y no una público, debemos especificar el nombre del módulo delante del nombre de la vista, por ejemplo:

    * **@K2Backend/default/index**        -> el módulo es K2/Backend y la vista es default/index.twig
    * **@K2EmailTemplate/usuarios/crear** -> el módulo es K2/EmailTemplate y la vista es usuarios/crear.twig
    * **@Twitter/base**                   -> el módulo es Twitter y el template es base.twig

El nombre del módulo es el nombre lógico que se le dá a los mismos en los config.php de cada uno.

cache()
______

:: 

    Controller->cache($time = false)

Establece el tiempo de caché para una vista ó controlador completos, se debe pasar un `intervalo de tiempo válido <http://www.php.net/manual/es/datetime.formats.relative.php>`_, si se pasa false, no se cachea. Por ejemplo:

.. code-block:: php

    //archivo app/modules/MiModulo/Controller/usuariosController.php

    namespace MiModulo\Controller;

    use K2\Kernel\Controller\Controller;

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

Cabe destacar que la cache solo se activa en produccíon.

render()
_______

:: 

    Controller->render($view, array $params = array(), $time = null)

LLama al servicio @view y nos devuelve la respuesta ya construida con la vista especificada. Se le pueden pasar parametros que serán las variables en la vista y un tiempo de cache.

Este método es util cuando queremos enviar la respuesta por correo por ejemplo. crear un PDF, etc.

renderNotFound()
_______________

:: 

    Controller->renderNotFound($message)

Este método lanza una excepcion NotFoundException, podemos mostrar un mensaje para verlo en el entorno de desarrollo, en producción se mostrará la vista 404.twig de "app/views/errors/"


