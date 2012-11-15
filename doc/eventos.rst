Eventos
=======

En esta nueva versión de KumbiaPHP existe la posibilidad de escuchar y disparar eventos, lo que brinda la oportunidad de extender y/ó cambiar las funcionalidades ofrecidas por el framework, ya que podemos ejecutar tareas en determinados puntos de la aplicación y controlar el flujo de la misma, ya sea deteniendo la ejecución de la patición, cambiando el rumbo de esta ultima, cambiando el controlador, la acción ó la respuesta a mostrar, entre otras muchas posibilidades más.

.. contents:: Índice:

Eventos del Framework
---------------------

Los eventos internos del framework son los siguientes:

    * kumbia.request
    * kumbia.controller
    * kumbia.response
    * kumbia.exception
    * activerecord.beforequery
    * activerecord.afterquery

Evento kumbia.request
_____________________
Evento kumbia.controller
________________________
Evento kumbia.response
______________________
Evento kumbia.exception
_______________________
Evento activerecord.beforequery
_______________________
Evento activerecord.afterquery
_______________________