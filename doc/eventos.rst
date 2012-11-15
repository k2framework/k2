Eventos
=======

En esta nueva versión de KumbiaPHP existe la posibilidad de escuchar y disparar eventos, lo que brinda la oportunidad de extender y/ó cambiar las funcionalidades ofrecidas por el framework, ya que podemos ejecutar tareas en determinados puntos de la aplicación y controlar el flujo de la misma, ya sea deteniendo la ejecución de la patición, cambiando el rumbo de esta ultima, cambiando el controlador, la acción ó la respuesta a mostrar, entre otras muchas posibilidades más.

.. contents:: Índice:

Eventos del Framework
---------------------
Evento kumbia.request
_____________________

El evento kumbia.request es ejecutado por el `kernel <https://github.com/manuelj555/Core/blob/master/src/KumbiaPHP/Kernel/Kernel.php>`_ al inicio de la patición, despues de iniciar los parametros y objetos básicos que necesita la aplicación para funcionar.

Este evento ofrece a los escuchas un objeto de tipo `KumbiaPHP\Kernel\Event\RequestEvent <https://github.com/manuelj555/Core/blob/master/src/KumbiaPHP/Kernel/Event/RequestEvent.php>`_ mediante el cual podemos obtener el objeto Request, establecer el response, detener la ejecucion de los siguientes llamados a los escuchas, etc.

Estableciendo una Respuesta
...........................

Este evento ofrece la posibilidad de establecer una respuesta en el objeto RequestEvent, de hacerlo el kernel no creará la instancia del controlador, pasando directamente a ejecutar el evento kumbia.response ( saltandose el evento kumbia.controller ), para continuar con la ejecución de los procesos restantes, esto es util para evitar la ejecución del controlador en casos especiales como páginas seguras, etc. Ejemplo:

.. code-block:: php

    //servicio @k2_seguridad

    namespace K2\Seguridad;

    use KumbiaPHP\Kernel\Response;

    class Seguridad
    {
        public function verificarAcceso(RequestEvent $event)
        {
            if ( !$this->sesionIniciada() ){
                $event->setResponse(new Response("Acceso Denegado", 403));
                $event->stopPropagation();
            }
        }
    }

Al establecer una respuesta en el objeto $event, no se ejecutará el controlador ni el evento kumbia.controller

Evento kumbia.controller
________________________
El evento kumbia.controller es ejecutado por el `kernel <https://github.com/manuelj555/Core/blob/master/src/KumbiaPHP/Kernel/Kernel.php>`_ despues de iniciar la instancia del controlador, y contiene el objeto request y la instancia del controlador.

Este evento ofrece a los escuchas un objeto de tipo `KumbiaPHP\Kernel\Event\ControllerEvent <https://github.com/manuelj555/Core/blob/master/src/KumbiaPHP/Kernel/Event/ControllerEvent.php>`_ mediante el cual podemos obtener el objeto Request, obtener/establecer la instancia del controlador, obtener/establecer el nombre de la acción a ejecutar en el controlador, obtener/establecer los parametros que serán pasados a la acción., detener la ejecucion de los siguientes llamados a los escuchas, etc.

Su principal función es cambiar el controlador, la accion, ó los parametros que se usarán para invocar al método del Controlador.

Evento kumbia.response
______________________
El evento kumbia.response es ejecutado por el `kernel <https://github.com/manuelj555/Core/blob/master/src/KumbiaPHP/Kernel/Kernel.php>`_ despues de ejecutar el controlador, y contiene el objeto request y el objeto response con el contenido de la respuesta ya establecido.

Este evento ofrece a los escuchas un objeto de tipo `KumbiaPHP\Kernel\Event\ResponseEvent <https://github.com/manuelj555/Core/blob/master/src/KumbiaPHP/Kernel/Event/ResponseEvent.php>`_ mediante el cual podemos obtener el objeto Request, obtener la instancia de la respuesta, etc...

Generalmente es usado para cambiar el contenido de la respuesta ( agregar ó quitar partes, para agregar un debug, algun menú, etc... ).

Evento kumbia.exception
_______________________
El evento kumbia.exception es ejecutado por el `kernel <https://github.com/manuelj555/Core/blob/master/src/KumbiaPHP/Kernel/Kernel.php>`_ cuando ocurre una excepción en la aplicación y está no es capturada, ofrece la instancia del request y la instancia de la excepcion que se lanzó.

Este evento ofrece a los escuchas un objeto de tipo `KumbiaPHP\Kernel\Event\ExceptionEvent <https://github.com/manuelj555/Core/blob/master/src/KumbiaPHP/Kernel/Event/ExceptionEvent.php>`_ mediante el cual podemos obtener el objeto Request, obtener la instancia de la excepcion, establecer una respuesta a mostrar, etc...

Evento activerecord.beforequery
_______________________________
Evento activerecord.afterquery
______________________________