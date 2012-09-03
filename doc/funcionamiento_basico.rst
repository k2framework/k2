Trabajando con KumbiaPHP 2
==========================

Realmente el principio básico del recorrido de las peticiones no cambia mucho con respecto a versiones anterirores del framework.

Donde tenemos un controlador que se ejecuta al enviar una petición a una url concreta, y nuestro controlador puede tener filtros para realizar tareas antes y despues de ejecutar la acción en el controlador. 

Los controladores en esta nueva versión ya no extienden de AppController ni AdminController, sino que extienden de una clase base para todos los controladores "KumbiaPHP\\Kernel\\Controller\\Controller" la cual ofrcee métodos utiles para interactuar y solicitar herramientas que el framework ofrece.

Cabe destacar que no es obligatorio que un controlador extienda de Controller, es decir si tenemos un controlador que no va a realizar ningún proceso en sus acciónes, no es necesario extender de Controller.

Se incorporan los escuchas de eventos; debido a que no tenemos un AppController en esta versión, no disponemos de un método que realize tareas para todas las peticiones de nuestra aplicación, sin embargo con la incorporación de los escuchas de eventos podemos facilmente realizar esas tareas y más, que anterirormente se hacian en el initialize del AppController.

Recorido de la Petición
-----------------------