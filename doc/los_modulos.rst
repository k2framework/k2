Los Modulos
========

.. contents:: En esta nueva versión de KumbiaPHP, la aplicación está constituida por módulos ó paquetes.


Que es un Modulo
----------------

Un módulo ó paquete no es más que una carpeta que contiene clases, carpetas y archivos de configuración que cumplen un objetivo en particular dentro de la aplicación.

Ejemplos de ellos son, un Backend, un API REST, un carrito de compras, etc.

Y no solo cumplen funcionalidades de tipo petición respuesta, sino que tambien pueden servir de módulos que ofrecen una funcionalidad que puede ser usada por cualquier otro módulo de la aplicación. Generalmente estos módulos tienen servicios para ofrecer esta caracteristica.

Ejemplo de este tipo de módulos son, un servicio de correo, un ACL, un traductor, logger ( FirePHP por ejemplo ), etc. Donde cualquier otro módulo ó clase puede hacer uso de estos servicios.

Nombre del Módulo
-----------------

Un módulo puede contener cualquier nombre válido como el que le damos a nuestras carpetas en el sistema, ya que por defecto el nonmbre del módulo va de la mano con el nombre de las carpetas que lo contienen.

Creando un Modulo
-----------------

Generalmente los módulos de nuestra aplicación estarán contenidos en la carpeta **"proyecto/app/modules/"**, sin embargo un módulo puede estar en cualquier parte del servidor, ya que los módulos deben ser registrados en el `AppKernel <app_kernel.rst>`_ para poder tener acceso a ellos.

Un ejemplo básico de la estructura de un módulo es:

::
	
	K2/Ventas
       |
       |---config.php
       |
	   |---Controller
	   |	   |-------indexController.php
	   |	   |-------reportesController.php
	   |
	   |-----Model
	   |	   |-------Ventas.php
	   |
	   |-----View
	   |	   |-------ventas
	   |	   |	       |----ultimas.twig
	   |	   |	       |----agregar.twig
	   |	   |	       |----eliminar.twig
	   |	   |-------reportes
	   |	    	       |----ventas_semanales.twig
	   |	    	       |----ventas_hoy.twig
	   |
	   |----MisServicios  	
	    	   |	
	    	   |		
	    	   |
	    	   |
	   	       |-----GestorVentas.php
	   	       |-----ImpresorVentas.php

		
Como podemos ver en el ejemplo tenemos un módulo llamado Ventas que contiene una serie de carpetas ( Ninguna de las carpetas es obligatoria ), de las cuales las carpetas Controller y View deben tener siempre esos nombres, ya que el framework busca los controladores y vistas dentro de las mismas. La carpeta Model contendrá los modelos, realmente no importa el nombre de la carpeta que contiene los modelos ó si estos se encuentran en carpeta alguna, ya que el autoload PSR-0 los buscará a traves de su namespace. Tambien tenemos una carpeta llamada MisServicios, donde su nombre no es relevante, y contiene los servicios que posee el módulo.

Ademas tenemos un archivo Module.php el cual es una clase debe extender obligatoriamente de **K2\\Kernel\\Module** y en esta podremos definir las configuraciones y servicios de nuestro módulo.

La clase Module
===============

Esta clase es obligatoria en cada módulo que creemos, puede tener cualquier nombre, y debe extender de **K2\\Kernel\\Module** ademas debe estar colocada en la raiz del módulo, es decir a la altura de las carpetas Controller, View, etc...

En ella podremos registrar parametros y servicios en el contenedor, ademas agregar escuchas de eventos y configuraciones adicionales para el módulo que se crea.

Ejemplo
.......

.. code::block php

    <?php

    namespace K2\Ventas;

    use K2\Kernel\Module;

    class VentasModule extends Module
    {

        //podemos reescribir el método init() para realizar configuraciones
        //en el módulo.
        public function init()
        {
            //acá registramos un servicio en el container
            $this->container->set('mi_servicio', function($c) {
                return new K2\Ventas\Servicio(); //devolvemos la instancia para el servicio
            });
        }

    }

En este ejemplo hemos creado una clase en K2/Ventas/VentasModule.php la cual extiende de K2\\Kernel\\Module y reescribe el método init(), lo cual no es obligatorio, solo lo reescribimos cuando queremos agregar configuración adicional como servicios ó parametros en el proyecto.

Ejemplo de Nombres de Modulos
_____________________________

Acá tenemos un ejemplo de la asociación entre el nombre del módulo y el espacio de nombre al que está asociado:

+----------------------------+-------------------------------------------------+
|     Nombre del Módulo      |  Ejemplos de espacios de nombres de la clases   |
+----------------------------+-------------------------------------------------+
|                            |  * K2\Backend\Controller\indexController        |
|                            |  * K2\Backend\Controller\usuariosController     |
|         K2/Backend         |  * K2\Backend\Model\Usuarios                    |
|                            |  * K2\Backend\Model\RolesRecursos               |
|                            |  * K2\Backend\Form\UsuarioForm                  |
+----------------------------+-------------------------------------------------+
|                            |  * K2\Calendar\Controller\indexController       |
|        K2/Calendar         |  * K2\Calendar\Controller\eventController       |
|                            |  * K2\Calendar\Model\Event                      |
+----------------------------+-------------------------------------------------+
|        K2/Debug            |  * K2\Debug\Service\Debug                       | 
+----------------------------+-------------------------------------------------+

Como se puede apreciar el nombre del módulo es tambien el inicio de los namespace en las clases.

Ahora, porque llamar al módulo **K2/Backend** y no simplemente **Backend**? Esto es así para asegurar que si otra persona ó empresa crea un módulo con el mismo nombre, no existan conflictos, es decir, si el módulo se llamara solo Backend y otra persona crea un módulo llamado Backend tambien, al intentar usar los 2 módulos en la aplicación se generarán conflictos de nombres en los namespaces, ademas no se podrán registrar los 2 módulos al mismo tiempo en el AppKernel.

Lo mejor siempre será entonces llamara al módulo con un identificador del usuario, grupo ó empresa delante del nombre del módulo, ejemplos:

	* **K2/Backend**: el módulo es un backend del grupo K2
	* **Manuel/Backend**: el módulo es un backend de manuel
	* **KumbiaPHP/Backend**: el módulo es un backend de KumbiaPHP

Instalando Modulos de Terceros
------------------------------

En esta nueva versión es muy facil instalar y configurar módulos de otras personas, ya sea para agregar alguna funcionalidad a la aplicación, ó para usar algun tipo de libreria creada por la comunidad.

Solo debemos descargar dicho módulo y colocarlo en la carpeta vendors de la aplicación si no vamos a editar el código del módulo, ó en la carpeta modules de la aplicación si vamos a editar dicho módulo.

Luego de esto debemos registrar el módulo en el archivo `app/AppKernel.php <https://github.com/k2framework/k2/blob/master/doc/app_kernel.rst>`_, ** en el método `registerModules() <https://github.com/k2framework/k2/blob/master/doc/app_kernel.rst#el-metodo-registermodules>`_.

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
            new \Index\IndexModule(),
            new \K2\Twitter\TwitterModule(), //esta clase debe estar en la raiz del módulo
        );
        
    }

    
    $loader->add('K2\\Twitter\\', __DIR__ . '/../../vendor/'); //lo registramos ademas en el autoload.



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
            new \Index\IndexModule(),
            new \K2\Backend\Backend(), //esta clase debe estar en la raiz del módulo
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

Dependiendo de la finalidad del módulo, existen dos lugares principales en los que alojar al mismo. Si nuestro módulo va a poder ser reutilizable en diferentes aplicaciones, y no está enfocado en una funcionalidad de una aplicación en especifico, lo mejor es que se encuentre en la carpeta **vendors** de los proyectos, ya que esto permite que varias aplicaciones puedan utilizar el mismo módulo conjuntamente.

Si el módulo ofrece una funcionalidad especifica dentro de la aplicación, por ejemplo los reportes de ventas de una empresa, lo mejor es que se encuentre alojado dentro de la carpeta **app/modules**, ya que el módulo es propio del proyecto, y los demas proyectos no lo reuzarán.
