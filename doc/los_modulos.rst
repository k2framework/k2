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

Un módulo puede contener cualquier nombre válido como el que le damos a nuestras carpetas en el sistema, ya que por defecto el nombre del módulo va de la mano con el nombre de las carpetas que lo contienen.

Creando un Modulo
-----------------

Generalmente los módulos de nuestra aplicación estarán contenidos en la carpeta **"proyecto/app/modules/"**, sin embargo un módulo puede estar en cualquier parte del servidor, ya que los módulos deben ser registrados en el `app/app.php <app.rst>`_ para poder tener acceso a ellos.

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

Ademas tenemos un archivo config.php el cual contiene la definición y la configuración del módulo.

EL archivo config.php
===============

Este archivo es obligatorio en cada módulo que creemos, puede tener cualquier nombre, y debe estar colocado en la raiz del módulo, es decir a la altura de las carpetas Controller, View, etc...

En el podremos registrar parametros y servicios en el contenedor, ademas agregar escuchas de eventos y configuraciones adicionales para el módulo que se crea.

**Ejemplo:**

.. code-block:: php

    <?php
    
    namespace K2\Ventas;
    
    use K2\Di\Container\Container;
    use K2\Kernel\Event\K2Events as E;
    use K2\Security\Event\Events as SE;
    
    return array(
        'name' => 'Index', //nombre lógico del módulo
        'namespace' => __NAMESPACE__, //el namespace que usa el módulo
        'path' => __DIR__, //el direcorito del módulo
        'parameters' => array(
            //parametros que serán insertados en el Container
        ),
        'services' => array(
            //servicios que ofrece el módulo
            'mi_servicio' => function(Container $c) {
                return new Services\Servicio($c);
            }
        ),
        'listeners' => array( //escuchas de eventos 
            SE::LOGIN => array(
                array('mi_servicio', 'onLogin')
            ),
            SE::LOGOUT => array(
                array('mi_servicio', 'cerrandoSesion')
            ),
        ),
        'init' => function(Container $c) { //configuración adicional del módulo
            //agregamos el servicio firewall al container
            $c->set('firewall', function($c) {
                        return new \K2\Security\Listener\Firewall($c);
                    });
            //hacemos que el firewall escuche las peticiones
            $c['event.dispatcher']->addListener(E::REQUEST, array('firewall', 'onKernelRequest'), 100);
        },
    );

En este ejemplo hemos creado un archivo **K2/Ventas/config.php**, en el cual definimos el nombre lógico de módulo, el namespace del mismo, los servicios, parametros y escuchas de eventos, y ademas alguna configuración adicional en el indice **'init'** el cual tiene como valor un clousure que espera el Container.

Nombres de Módulos

Generalmente los módulos tendrán asociado un vendor delante del nombre de los mismos, esto es asó debido a que si otra persona ó empresa crea un módulo con el mismo nombre, no existan conflictos, es decir, si el módulo se llamara solo Backend por ejemplo y otra persona crea un módulo llamado Backend tambien, al intentar usar los 2 módulos en la aplicación se generarán conflictos de nombres en los namespaces.

Lo mejor siempre será entonces llamara al módulo con un identificador del usuario, grupo ó empresa delante del nombre del módulo, ejemplos:

	* **K2Backend**: el módulo es un backend del grupo K2
	* **ManuelBackend**: el módulo es un backend de manuel

Instalando Modulos de Terceros
------------------------------

Es muy facil instalar y configurar módulos de otras personas, ya sea para agregar alguna funcionalidad a la aplicación, ó para usar algun tipo de libreria creada por la comunidad.

Solo debemos descargar dicho módulo y colocarlo en la carpeta vendors de la aplicación si no vamos a editar el código del módulo, ó en la carpeta modules de la aplicación si vamos a editar dicho módulo.

Luego de esto debemos registrar el módulo en el archivo `app/config/app.php <https://github.com/k2framework/k2/blob/master/doc/app.rst>`_.

Registrando el módulo
_________________________

Cuando queremos agregar un módulo a nuestra aplicación debemos hacerlo en el archivo **app/config/app.php**.

Veamos un ejemplo de como lograr esto::

    Queremos instalar el módulo (plugin) K2/Twitter, el cual nos ofrece un api de conexión con twitter.

    veamos como agregarlo al app/config/app.php, suponiendo que lo colocamos en vendor:

.. code-block:: php

    /* * *****************************************************************
     * Iinstalación de módulos
     */
    App::modules(array(
        '/' => include APP_PATH . '/modules/Index/config.php',
        '/twitter' => include dirname(APP_PATH) . '/vendor/K2/Twitter/config.php',
    ));


Si el módulo que acabamos de registrar es accesible desde el navegador, el indice del mismo será usado como prefijo de ruta para acceder a los controladores del mismo. Si no queremos que sea accesible desde el navegador, no le colocamos ningun incide.

Donde debo colocar Los Modulos
------------------------------

Dependiendo de la finalidad del módulo, existen dos lugares principales en los que alojar al mismo. Si nuestro módulo va a poder ser reutilizable en diferentes aplicaciones, y no está enfocado en una funcionalidad de una aplicación en especifico, lo mejor es que se encuentre en la carpeta **vendors** de los proyectos, ya que esto permite que varias aplicaciones puedan utilizar el mismo módulo conjuntamente.

Si el módulo ofrece una funcionalidad especifica dentro de la aplicación, por ejemplo los reportes de ventas de una empresa, lo mejor es que se encuentre alojado dentro de la carpeta **app/modules**, ya que el módulo es propio del proyecto, y los demas proyectos no lo reuzarán.
