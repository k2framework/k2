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

.. code-block:: php

    //archivo app/modules/MiModulo/Controller/usuariosController.php

    namespace MiModulo\Controller;

    use KumbiaPHP\Kernel\Controller\Controller;

    class usuariosController extends Controller
    {
		public function index_action()
		{
			//se mostrará la vista app/modules/MiModulo/View/Usuarios/index.phtml
		}
		
		public function crear_action()
		{
			//se mostrará la vista app/modules/MiModulo/View/Usuarios/crear.phtml
		}
		
		public function editar_action()
		{
			$this->setView("crear"); //se cambia la vista a renderizar.
		
			//se mostrará la vista app/modules/MiModulo/View/Usuarios/crear.phtml
		}
    }

Donde debe ir la vista
______________________

Supongamos que tenemos un módulo llamado Compras, y tenemos un controlador llamado articulosController en "Compras/Controller/articulosController.php", ademas dicho controlador tiene tres métodos, index(), ver(), agregar().

Las vistas para ese controlador deben ir en:

	* **Compras/View/articulos/index.phtml**
	* **Compras/View/articulos/ver.phtml**
	* **Compras/View/articulos/agregar.phtml**

Los Templates
-------------

Los templates son archivos con extensión .phtml que contienen la parte comun de la vista en muchas acciones, es decir, son aquellas partes de la representación de la vista que se mantiene igual, independientemente de contenido mostrado por una acción particular, por ejemplo:

    * La cabecera de una Página
    * El pié
    * El menú
    * la inclusión de los css y javascritps
    * Etc.

Todas estas son partes comunes dentro de una vista ( En este caso un Html ).

Nombre del Template
___________________

El nombre del template por defecto es **default** y se encuentra dentro de **app/view/templates/default.phtml**, ademas podemos tener tantos templates como queramos en nuestra aplicacion, para cambiar de template solo debemos llamar al método `setTemplate() <https://github.com/manuelj555/k2/blob/master/doc/controlador.rst#settemplate>`_ en nuestro controlador.

Donde debe ir el Template
_________________________

Los templates pueden ir en dos partes dentro de nuestra aplicación, una es en la parte publica de la app, y el otro sitio es en la carpeta templates de los módulos usados en el proyecto.
    
    * templates publicos: **/proyecto/app/view/templates/**
    * templates privados: **/Carpeta_del_Modulo/View/_shared/templates/**

Como se puede apreciar, esos son los 2 lugares donde se pueden almacenar los templates en los proyectos.

Templates Publicos y de Modulos
_______________________________

En nuestras aplicaciones podemos tener tanto templates genarales (que no pertenecen a un módulo en particular), como templates que se encuentran dentro de la carpeta de vistas de un módulo en especifico.

Realmente si en nuestro proyecto no vamos a realizar módulos que puedan ser usados por otros, podemos tranquilamente colocar nuestros templates en la carpeta app/view/templates/ de nuestro proyecto, ya que la utilidad de los templates de módulo es que las vistas de un módulo lo usen en distintas aplicaciones, es decir, si instalamos el módulo en otros proyectos, el template acompañara al módulo y todo funcioará correctamente.

Patials o Vistas Parciales
--------------------------

Donde debe ir el Partial
________________________

Helpers
-------

Los Helpers son clases estáticas que ofrecen métodos utiles que facilitan la creacion de ciertos elementos html como links, imagenes, inserccion de stripts y css, creacion de elementos de formularios, etc. A continuación se listan los helpers disponibles:

	* `Html <https://github.com/manuelj555/k2_core/blob/master/src/KumbiaPHP/View/Helper/Html.php>`_
	* `Form <https://github.com/manuelj555/k2_core/blob/master/src/KumbiaPHP/View/Helper/Form.php>`_
	* `Tag <https://github.com/manuelj555/k2_core/blob/master/src/KumbiaPHP/View/Helper/Tag.php>`_
	* `Js <https://github.com/manuelj555/k2_core/blob/master/src/KumbiaPHP/View/Helper/Js.php>`_
	* `Ajax <https://github.com/manuelj555/k2_core/blob/master/src/KumbiaPHP/View/Helper/Ajax.php>`_

Las Funciones h() y eh()
-----------------------

Estas dos funciones ofrecen alias para escapar cadenas de texto y mostrarlas, ejemplos:

.. code-block:: php

	<?php

	echo h("<h1>hola cómo estás</h1>"); //muestra el texto escapado, es decir el <h1> se imprime como texto.
	$txt = h("<span class='span3'>Mensaje</span>"); //escapa el span y lo devuelve como texto
	eh("mensaje"); //es como hacer echo h("mensaje") ó echo htmlspecialchars("mensaje");