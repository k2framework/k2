Los Modulos
===========

.. contents:: En esta nueva versión de KumbiaPHP, la aplicación está constituida por módulos ó paquetes.


Que es un Modulo
----------------

Un módulo ó paquete no es más que una carpeta que contiene clases, carpetas y archivos de configuración que cumplen un objetivo en particular dentro de la aplicación.

Ejemplos de ellos son, un Backend, un API REST, un carrito de compras, etc.

Y no solo cumplen funcionalidades de tipo petición respuesta, sino que tambien pueden servir de modulos que ofrecen una funcionalidad que puede ser usada por cualquier otro módulo de la aplicación. Generalmente estos módulos tienen servicios para ofrecer esta caracteristica.

Ejemplo de este tipo de módulos son, un servicio de correo, un ACL, un traductor, logger ( FirePHP por ejemplo ), etc. Donde cualquier otro módulo ó clase puede hacer uso de estos servicios.


Creando un Modulo
-----------------

Generalmente los módulos de nuestra aplicación estarán contenidos en la carpeta "proyecto/app/modules/", sin embargo un módulo puede estar en cualquier parte del servidor, ya que en esta nueva versión los módulos deben ser registrados en el `AppKernel <app_kernel.rst>`_ para poder tener acceso a ellos.

Un ejemplo básico de la estructura de un módulo es:

::
	
	Ventas
	   |---Controller
	   |	   |-------indexController.php
	   |	   |-------reportesController.php
	   |
	   |-----Model
	   |	   |-------Ventas.php
	   |
	   |-----View
	   |	   |-------Ventas
	   |	   |	       |----ultimas.phtml
	   |	   |	       |----agregar.phtml
	   |	   |	       |----eliminar.phtml
	   |	   |-------Reportes
	   |	   |	       |----ventas_semanales.phtml
	   |	   |	       |----ventas_hoy.phtml
	   |	   |-------_shared
	   |	   	        |----errors
	   |			|----templates
	   |----MisServicios    |----partiales	
	   |	   |		|----....
	   |	   |		
	   |	   |
	   |	   |
	   |	   |-----GestorVentas.php
	   |	   |-----ImpresorVentas.php
	   |
	   |----config
		   |-----config.ini
		   |-----services.ini
		
Como podemos ver en el ejemplo tenemos un módulo llamado Ventas que contiene una serie de carpetas ( Ninguna de las carpetas es obligatoria ), de las cuales las carpetas Controller y View deben tener siempre esos nombres, ya que el framework busca los controladores y vistas dentro de las mismas. La carpeta Model contendrá los modelos, realmente no importa el nombre de la carpeta que contiene los modelos ó si estos se encuentran en carpeta alguna, ya que el autoload PSR-0 los buscará a traves de su namespace. Tambien tenemos una carpeta llamada MisServicios, donde su nombre no es relevante, y contiene los servicios que posee el módulo.

Por ultimo tenemos la carpeta config, y puede tener dos archivos, config.ini y services.ini, en el primero podemos definir parametros de configuración para el módulo y en services.ini estarán definidos los servicios que ofrece nuestro módulo, que para el ejemplo son el servicio GestorVentas y el servicio ImpresorVentas.

Instalando Modulos de Terceros
------------------------------

En esta nueva versión es muy facil instalar y configurar módulos de otras personas, ya sea para agregar alguna funcionalidad a la aplicación, ó para usar algun tipo de libreria creada por la comunidad.

Solo debemos descargar dicho módulo y colocarlo en la carpeta vendors de la aplicación si no vamos a editar el código del módulo, ó en la carpeta modules de la aplicación si vamos a editar dicho módulo.

Luego de esto debemos registrar el módulo en el archivo `app/AppKernel.php <https://github.com/manuelj555/k2/blob/master/doc/app_kernel.rst>`_, ** en el método `registerModules() <https://github.com/manuelj555/k2/blob/master/doc/app_kernel.rst#el-metodo-registermodules>`_.

En el registerModules()
_________________________

Cuando queremos agregar un módulo a nuestra aplicación debemos hacerlo en el método registerModules().

Veamos un ejemplo de como lograr esto::

    Queremos instalar el módulo (plugin) K2/Twitter, el cual nos ofrece un api de conexión con twitter.

    veamos como agregarlo al AppKernel, suponiendo que lo colocamos en vendor:

.. code-block:: php

    protected function registerModules()
    {
        $modules = array(
            'KumbiaPHP'   => __DIR__ . '/../../vendor/kumbiaphp/core/src/',
            'Index'       => __DIR__ . '/modules/',
            ...
            'K2/Twitter'   => __DIR__ . '/../../vendor/',
        );
        ...
    }

Con esto ya tenemos instalado el módulo en la aplicación.


En el registerRoutes()
_____________________

Si el módulo que acabamos de registrar es accesible desde el navegador, debemos crear un prefijo de ruta para poder acceder a el, esto lo hacemos en el método registerRoutes()

.. code-block:: php

    //archivo AppKernel.php
    //estamos registrando el módulo K2/Backend, 
    //ademas le asignamos el prefijo de ruta /admin
    //por lo que toda ruta que comienze con /admin* cargará ese módulo.

    protected function registerModules()
    {
        $modules = array(
            'KumbiaPHP'   => __DIR__ . '/../../vendor/kumbiaphp/core/src/',
            'Index'       => __DIR__ . '/modules/',
            ...
            'K2/Backend'   => __DIR__ . '/modules/',
        );
        ...
    }
    protected function registerRoutes()
    {
        return array(
            '/'                 => 'Index',
            ...
            '/admin'                 => 'K2/Backend',
        );
    }

Donde debo colocar Los Modulos
------------------------------
