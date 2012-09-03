El Controlador
==============

Los controladores en KumbiaPHP 2 son muy parecidos a los controladores de la versión 1 del framework, estan compuestos de métodos que si concuerdan con el patrón de la url de una petición, el kernel del framework los invoca y le pasa los parametros que estos soliciten.

Ademas se siguen teniendo los filtros pre y post ejecución de la acción correspondiente.

Nombre de la Clase
------------------

En esta versión del framework, tanto los nombres de clases como nombres de archivos se escriben exactamente igual. Preferiblemente en notación CamelCase ( Al menos para los controladores el CamelCase es Obligatorio ).

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

Nombres para las acciones
_________________________

Los Filtros
-----------

beforeFilter
____________

afterFilter
___________