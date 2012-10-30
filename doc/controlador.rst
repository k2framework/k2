El Controlador
==============

Los controladores en KumbiaPHP 2 son muy parecidos a los controladores de la versión 1 del framework, estan compuestos de métodos que si concuerdan con el patrón de la url de una petición, el kernel del framework los invoca y le pasa los parametros que estos soliciten.

Ademas se siguen teniendo los filtros pre y post ejecución de la acción correspondiente.

Nombre de la Clase
------------------

En esta versión del framework, tanto los nombres de clases como nombres de archivos se escriben exactamente igual. Preferiblemente en notación CamelCase ( Al menos para los controladores el CamelCase es Obligatorio ).

Ejemplo de un Controlador
_________________________

::

    //archivo app/modules/MiModulo/Controller/UsuariosController.php
    <?php

    namespace MiModulo\Controller;

    use KumbiaPHP\Kernel\Controller\Controller;

    class UsuariosController extends Controller //ahora se extiende de una clase base Controller.
    {
        public function index()
        {
            $this->mensaje = "Hola Mundo...!!!";
        }
    }

Este es un ejemplo de un controlador llamado UsuariosController, el cual extiende de la clase base Controller, y tiene un método llamado index() que crea una variable "mensaje" con el valor "Hola Mundo...!!!".

Como debe ser la Ruta para acceder a un Controlador
___________________________________________________

Debido a que los nombres de los archivos y clases de controladores son en CamelCase, debe haber alguna manera de que sin usar esta notación en la url, el kernel pueda encontrar y ejecutar al controlador solicitado. 

Esto se logra haciendo una conversión de la ruta, que debe estár en small_case, a CamelCase, veamos algunos ejemplos

::

    Supongamos que estamos en el Módulo Usuarios:

    Modulo   / controlador / acción             =>      Controlador a ejecutar:

    /usuarios                                   =>        IndexController
    /usuarios/index                             =>        IndexController
    /usuarios/index/index                       =>        IndexController
    /usuarios/admin/index                       =>        AdminController
    /usuarios/nuevos_ingresos/                  =>        NuevosIngresosController
    /usuarios/nuevos_ingresos/index             =>        NuevosIngresosController

Como se puede apreciar las rutas siempre estan en minuscula, y en notación small_case, mientras que los controladores están en CamelCase, entonces el kernel, al estudiar la url convertirá el patrón de ruta del controlador en CamelCase, para encontrar y llamar al mismo de existir.

Las Acciónes
------------

Una acción es un método de la clase controladora que puede ser ejecutada por el framework, si se cumplen ciertas condiciones en la url de la petición.

Cabe destacar que las acciónes para poder ser accedidas desde la Url, deben ser métodos públicos.

Nombres para las acciones
_________________________

En esta versión los nombres de las acciónes son camelCase ( la primera letra en minuscula ), esto para seguir con el estandar de codificación usado en la mayoría de frameworks y librerias de PHP.

Como debe ser la Ruta para acceder a una Acción
___________________________________________________

Al igual que con los controladores, el kernel del framework hace una conversión de la ruta para convertirla en un nombre de acción válido en camelCase, veamos algunos ejemplos

::

    Supongamos que estamos en el Módulo Usuarios, controlador IndexController:

    Modulo   / controlador / acción             =>      Controlador a ejecutar:   =>    Acción a ejecutar

    /usuarios                                   =>        IndexController         =>         index()
    /usuarios/index                             =>        IndexController         =>         index()
    /usuarios/index/index                       =>        IndexController         =>         index()
    /usuarios/index/crear                       =>        IndexController         =>         crear()
    /usuarios/index/nuevo_ingreso               =>        IndexController         =>         nuevoIngreso()
    /usuarios/index/modificar_perfil            =>        IndexController         =>         modificarPerfil()

Como se puede apreciar las rutas siempre estan en minuscula, y en notación small_case, mientras que las acciones están en camelCase, entonces el kernel, al estudiar la url convertirá el patrón de ruta de la acción en camelCase, para encontrar y llamar a la misma de existir.

Los Filtros
-----------

Los filtros en los controladores son métodos protegidos ó privados que se ejecutan antes y/o despues de la ejecución de la acción del controlador.

Son útiles para verificar que se cumplan ciertas condiciones para ejecutar la acción, ó realizar tareas que son comunes en un controlador y que no queremos repetir en cada acción del mismo.

beforeFilter
____________

El método beforeFilter() es una función que puede tener una clase controladora y que, de existir, el framework llamará y ejecutará justo antes de realizar el llamado y ejecución de la acción solicitada en la petición.

Este método ofrece la posibilidad de cambiar ó evitar la ejecución de una acción, esto se logra devolviendo una cadena con el nombre de la nueva acción a ejecutar ( en el caso de que queramos cambiar la ejecución de la acción actual por otra ), ó devolviendo FALSE si no queremos que se ejecute la acción del controlador.

afterFilter
___________

El método afterFilter() es una función que puede tener una clase controladora y que, de existir, el framework llamará y ejecutará justo despues de realizar el llamado y ejecución de la acción solicitada en la petición.

NOTA: si el método beforeFilter() devuelve FALSE, este filtró no será ejecutado por el kernel del framework.

Parametros de las Acciones
--------------------------

Una acción de un controlador puede tener parametros ó argumentos que esperan ciertos datos de una petición, un ejemplo de esto es el ID de un registro que queremos editar en un CRUD. el framework obtiene los valores para estos argumentos a traves de la URL, donde cada valor pasado por la url despues del nombre de la acción es un parametro de la misma, estos valores deben ir separados por un / unos de otros, veamos unos ejemplos:

::

    <?php  //controlador app/modules/Home/Controller/UsuariosController.php

    namespaces Home\Controller;

    use KumbiaPHP\Kernel\Controller\Controller;

    class UsuariosController extends Controller
    {
        //   Ejemplos de url:
        //  /home/usuarios/editar/5   válida
        //  /home/usuarios/editar/10  válida
        //  /home/usuarios/editar/    invalida, el método espera el parametro id, por lo que se lanzará una excepcion
        public function editar($id){ //nuestra acción editar recibira en el parametro $id el valor 5
            ...
        }

        //   Ejemplos de url:
        //  /home/usuarios/fecha/10-10-2012   válida
        //  /home/usuarios/fecha/20-10-2012   válida
        //  /home/usuarios/fecha/             válida, si no se pasa el parametro, el mismo toma el valor por defecto.
        public function fecha($fecha = 'now'){ //nuestra acción espera el parametro fecha, si no lo recibe toma "now"
            $filtro = new DateTime($fecha); 
            ...
        }

        //   Ejemplos de url:
        //  /home/usuarios/filtrar_entre/03-05-2012/20-12-2012   válida
        //  /home/usuarios/filtrar_entre/20-10-2012/10-08-2012   válida
        //  /home/usuarios/filtrar_entre/                        invalida
        public function filtrarEntre($fechaInico, $fechaFinal){
            ...
        }
    }

La clase base Controller
-------------------------

Todos los controladores de la aplicación deben extender de la clase base "KumbiaPHP\Kernel\Controller\Controller", si no lo hacen, el framework lanzará una excepción indicandonos que debemos extender de dicha clase.

Esta clase ofrece ciertos métodos de gran utilidad para ser usados por los controladores de la aplicación, a continuación se detallarán cada uno de ellos:

get($id)
_______

getRequest()
___________

getRouter()
__________

getView()
_________

setView($view, $template = FALSE)
________________________________

getTemplate()
____________

setTemplate($template)
_____________________

cache($time = FALSE)
___________________

render(array $params = array(), $time = NULL)
____________________________________________

renderNotFound($message)
_______________________


