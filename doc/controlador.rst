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

    namespace MiModulo\\Controller;

    use KumbiaPHP\\Kernel\\Controller\\Controller;

    class UsuariosController extends Controller //ahora se extiende de una clase base Controller.
    {
        public function index()
        {
            $this->mensaje = "Hola Mundo...!!!";
        }
    }

Este es un ejemplo de un controlador llamado UsuariosController, el cual extiende de la clase base Controller ( esto no es obligatorio ), y tiene un método llamado index() que crea una variable "mensaje" con el valor "Hola Mundo...!!!".

Como debe ser la Ruta para acceder a un Controlador
___________________________________________________

Debido a que los nombres de los archivos y clases de controladores son en CamelCase, debe haber alguna manera de sin usar esta notación en la url, el kernel pueda encontrar y ejecutar al controlador solicitado. 

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

beforeFilter
____________

El método beforeFilter() es una función que puede tener una clase controladora

afterFilter
___________