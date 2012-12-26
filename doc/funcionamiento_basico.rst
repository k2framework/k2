Trabajando con KumbiaPHP 2
==========================
.. contents:: Contenido:

Realmente el principio básico del recorrido de las peticiones no cambia mucho con respecto a versiones anterirores del framework.

Donde tenemos un controlador que se ejecuta al enviar una petición a una url concreta, y nuestro controlador puede tener filtros para realizar tareas antes y despues de ejecutar la acción en el controlador. 

Los controladores en esta nueva versión ya no extienden de AppController ni AdminController, sino que extienden de una clase base para todos los controladores "K2\\Kernel\\Controller\\Controller" la cual ofrcee métodos utiles para interactuar y solicitar herramientas que el framework ofrece.

Se incorporan los escuchas de eventos; debido a que no tenemos un AppController en esta versión, no disponemos de un método que realize tareas para todas las peticiones de nuestra aplicación, sin embargo con la incorporación de los escuchas de eventos podemos facilmente realizar esas tareas y más, que anterirormente se hacian en el initialize del AppController.

Recorido de la Peticion
-----------------------
::

    |>->-> Cliente Petición.  
    |              |
    |       Se Inicia Kernel  -<-<-<-<-<-<-<--<-<-<-<-<-<-<-<-<-<-<-<-<--<-<-<-<-<|-<--<-<-<|           
    |       _______|________                                                      |         |          
    |      | Evento request |                                                     |         |          
    |       ----------------                                                      |         |          
    |              |                                                              |   Redirect a la    
    |      Firewall Activo? > Si -> Url protegida? > Si ->->->|                   |   Url original
    |              | NO                    | NO               |                   |         |          
    |              |                       |                  |                   |         |          
    |              |                       |<-<-<-< Si < Está logueado?           |         |          
    |              |                       |                  |NO                 |         |                  
    |              |                       |                  |                   |         |          
    |              |<-<-<-<-<-<-<-<-<-<-<-<|                  |               Redirect al   |
    |              |                                          |               form Logueo   |
    |              |                                          |                   |         |
    |              |                                  Intentando Logueo? > NO ->->|         |
    |              |                                          |Si                 |         |
    |              |                                          |                   |         |
    |              |                                     Logueo Válido? > NO ->->-|         |
    |              |                                          |Si                           |
    |              |                                          |                             |
    |         Controlador                                     ->->->->->->->->->->->->->->->|                 
    |       Devolvió Respuesta? > NO->->->-|
    |              |Si                     |
    |              |                 Servico @view
    |              |                 crea respuesta.
    |              |                       |
    |              |<-<-<-<-<-<-<-<-<-<-<-<|
    |              |
    |        Evento Response
    |              |
    |<-<- Retornamos la Respuesta.