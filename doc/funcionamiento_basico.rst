Trabajando con KumbiaPHP 2
==========================

Realmente el principio básico del recorrido de las peticiones no cambia mucho con respecto a versiones anterirores del framework.

Donde tenemos un controlador que se ejecuta al enviar una petición a una url concreta, y nuestro controlador puede tener filtros para realizar tareas antes y despues de ejecutar la acción en el controlador. 

Los controladores en esta nueva versión ya no extienden de AppController ni AdminController, sino que extienden de una clase base para todos los controladores "KumbiaPHP\\Kernel\\Controller\\Controller" la cual ofrcee métodos utiles para interactuar y solicitar herramientas que el framework ofrece.

Cabe destacar que no es obligatorio que un controlador extienda de Controller, es decir si tenemos un controlador que no va a realizar ningún proceso en sus acciónes, no es necesario extender de Controller.

Se incorporan los escuchas de eventos; debido a que no tenemos un AppController en esta versión, no disponemos de un método que realize tareas para todas las peticiones de nuestra aplicación, sin embargo con la incorporación de los escuchas de eventos podemos facilmente realizar esas tareas y más, que anterirormente se hacian en el initialize del AppController.

Creando un Módulo
-----------------

Un módulo no es más que una carpeta dentro de nuestra aplicación que contiene las carpetas y archivos necesarios para la ejecución de ciertas tareas en la aplicación. un ejemplo básico de la estructura de un módulo es:

::
	
	Ventas
	   |---Controller
	   |	   |-------IndexController
	   |	   |-------ReportesController
	   |
	   |-----Model
	   |	   |-------Ventas
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
	   |			|
	   |----MisServicios    |	
	   |	   |		|----templates
	   |	   |		|----partiales
	   |	   |		|----....
	   |	   |
	   |	   |-----GestorVentas
	   |	   |-----ImpresorVentas
	   |
	   |----config
		   |-----config.ini
		   |-----services.ini
		
Como podemos ver en el ejemplo tenemos un módulo llamado Ventas que contiene una serie de carpetas ( Ninguna de las carpetas es obligatoria ), de las cuales las carpetas Controller y View deben tener siempre esos nombres, ya que el framework busca los controladores y vistas dentro de las mismas. La carpeta Model contendrá los modelos, realmente no importa el nombre de la carpeta que contiene los modelos ó si estos se encuentran en carpeta alguna, ya que el autoload PSR-0 los buscará a traves de su namespace. Tambien tenemos una carpeta llamada MisServicios, donde su nombre no es relevante, y contiene los servicios que posee el módulo.

Por ultimo tenemos la carpeta config, esta en los módulos puede tener dos archivos, config.ini y services.ini, en el primero podemos definir parametros de configuración para el módulo y en services.ini estarán definidos los servicios que ofrece nuestro módulo, que para el ejemplo son el servicio GestorVentas y el servicio ImpresorVentas.