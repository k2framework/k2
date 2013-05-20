Trabajando con K2Framework
==========================
.. contents:: Contenido:

El principio básico del recorrido de las peticiones es el siguiente:

Tenemos un controlador que se ejecuta al enviar una petición a una url concreta, y nuestro controlador puede tener filtros para realizar tareas antes y despues de ejecutar la acción en el controlador. 

Los controladores extienden de una clase base para todos los controladores **"K2\\Kernel\\Controller\\Controller"** la cual ofrcee métodos utiles para interactuar y solicitar herramientas que el framework ofrece.

Se incorporan los escuchas de eventos, los mediante los cuales podemos facilmente realizar ciertas, que necesitemos se ejecuten en distintos momentos del recorrido de la petición.

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
