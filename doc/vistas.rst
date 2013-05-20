La lib View
================

.. contents:: La libreria View se encarga del proceso de busqueda, ejecución y renderizado de las vistas en la aplicación, es decir, esta libreria es quien llama a la vista seleccionada en el controlador, ó llama a la vista por defecto que se usará si no se cambia en el mismo, para el renderizado de las vistas se usa la libreria `Twig <http://twig.sensiolabs.org/>`_. Luego de ejecutada la vista, se crea un objeto response donde coloca el html final, establece las cabeceras http necesarias y devuelve el objeto al core de la aplicación para que esté disponible en el evento kumbia.response y finalmente dicha respuesta sea enviada.

Las Vistas
----------

Las vistas son archivos con extensión .twig que se encuentran en la carpeta "View/NombreControlador/archivo.twig" de cada módulo de la aplicación.

En ella se encuentra el código (HTML, XML, JSON, PDF, etc) que va a ser devuelto por el framework, es decir, la vista representa el contenido de la respuesta de la petición.

Nombre de la Vista
__________________

El nombre de la vista por defecto debe ser igual al nombre de la acción (método de la clase controladora) ejecutada en la petición. Sin embargo en el controlador se puede cambiar la vista a mostrar con `setView() <https://github.com/k2framework/k2/blob/master/doc/controlador.rst#setview>`_.

.. code-block:: php

    //archivo app/modules/MiModulo/Controller/usuariosController.php

    namespace MiModulo\Controller;

    use K2\Kernel\Controller\Controller;

    class usuariosController extends Controller
    {
		public function index_action()
		{
			//se mostrará la vista app/modules/MiModulo/View/Usuarios/index.twig
		}
		
		public function crear_action()
		{
			//se mostrará la vista app/modules/MiModulo/View/Usuarios/crear.twig
		}
		
		public function editar_action()
		{
			$this->setView("@MiModulo/usuarios/crear"); //se cambia la vista a renderizar.
		
			//se mostrará la vista app/modules/MiModulo/View/Usuarios/crear.twig
		}
    }

Donde debe ir la vista
______________________

Supongamos que tenemos un módulo llamado Compras, y tenemos un controlador llamado articulosController en "Compras/Controller/articulosController.php", ademas dicho controlador tiene tres métodos, index(), ver(), agregar().

Las vistas para ese controlador deben ir en:

	* **Compras/View/articulos/index.twig**
	* **Compras/View/articulos/ver.twig**
	* **Compras/View/articulos/agregar.twig**
