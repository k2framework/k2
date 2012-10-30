Los Servicios
=============

Los Servicios no son más que clases php que realizan y ofrecen funcionalidades especificas dentro de una aplicación, por ejemplo una clase para envio de correos, una clase para generar Pdfs, para cache, logs, sesión, validación, etc.

La gran ventaja de usar estas clases como servicios radica en que las clases que los usen no tienen que crear la instancia del servicio ni preocuparse de que les lleguen, de esto se encarga una libreria del framework llamada `Inyector de Dependencias <http://es.wikipedia.org/wiki/Inyecci%C3%B3n_de_dependencias>`_.

Ademas los servicios tambien pueden depender de otros servicios, entonces el inyector al crear las instancias de los primeros les proporciona las instancias que estos necesitan.

.. contents:: Índice:

Definiendo un Servicio
----------------------

Los servicios se definen en un archivo llamado services.ini que se puede encontrar en "proyecto/app/config/services.ini" y/ó dentro de la carpeta config de cada módulo. Ejemplos:

  * app/config/services.ini
  * app/moudles/K2/Backend/config/services.ini
  * app/modules/Index/config/services.ini

En cada uno de esos archivos se pueden definir servicios que luego serán creados por el framework solo si son necesitados.

Se debe tener cuidado al nombrar los servicios, ya que si 2 servicios tienen el mismo nombre, el ultimo en ser leido por el fw de los services.ini va a ser el que se creé realmente.

Nombre del Servicio
___________________

Estableciendo Dependencias
--------------------------

Escuchando Eventos
------------------