La lib View
================

.. contents:: La libreria View se encarga del proceso de busqueda, ejecución y renderizado de las vistas, los templates y partials de nuestras aplicaciones, es decir, esta libreria es quien llama a la vista seleccionada en el controlador, ejecuta su código y lo incrusta dentro del partial especificado tambien en el controlador, luego de esto, crear un objeto response donde coloca el html final, establece las cabeceras http necesarias y devuelve el objeto al core de la aplicación para que esté disponible en el evento kumbia.response y finalmente dicha respuesta sea enviada.

Ademas ofrece una serie de clases estáticas llamadas helpers ó ayudantes que ofrecen métodos utiles que facilitan la creacion de ciertos elementos html como links, imagenes, inserccion de stripts y css, creacion de elementos de formularios, etc.

Las Vistas
----------

Las vistas son archivos con extensión .phtml que se encuentran en la carpeta "View/NombreControlador/archivo.phtml" de cada módulo de la aplicación.

En ella se encuentra el código (HTML, XML, JSON, PDF, etc) que va a ser devuelto por el framework, es decir, la vista representa una parte ( El template y los partials complementas las demas partes ) de la respuesta de la petición.

Nombre de la Vista
__________________

El nombre de la vista por defecto debe ser igual al nombre de la acción (método de la clase controladora) ejecutada en la petición. Sin embargo en el controlador se puede cambiar la vista a mostrar con `setView() <https://github.com/manuelj555/k2/blob/master/doc/controlador.rst#setview>`_.

::

    //archivo app/modules/MiModulo/Controller/UsuariosController.php
    <?php

    namespace MiModulo\Controller;

    use KumbiaPHP\Kernel\Controller\Controller;

    class UsuariosController extends Controller
    {
		public function index()
		{
			//se mostrará la vista app/modules/MiModulo/View/Usuarios/index.phtml
		}
		
		public function crear()
		{
			//se mostrará la vista app/modules/MiModulo/View/Usuarios/crear.phtml
		}
		
		public function editar()
		{
			$this->setView("crear"); //se cambia la vista a renderizar.
		
			//se mostrará la vista app/modules/MiModulo/View/Usuarios/crear.phtml
		}
    }

Donde debe ir la vista
______________________

Supongamos que tenemos un módulo llamado Compras, y tenemos un controlador llamado ArticulosController en "Compras/Controller/ArticulosController.php", ademas dicho controlador tiene tres métodos, index(), ver(), agregar().

Las vistas para ese controlador deben ir en:

	* **Compras/View/Articulos/index.phtml**
	* **Compras/View/Articulos/ver.phtml**
	* **Compras/View/Articulos/agregar.phtml**

Los Templates
-------------

Helpers
-------