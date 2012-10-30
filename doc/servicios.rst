Los Servicios
=============

Los Servicios no son más que clases php que realizan y ofrecen funcionalidades especificas dentro de una aplicación, por ejemplo una clase para envio de correos, una clase para generar Pdfs, para cache, logs, sesión, validación, etc.

La gran ventaja de usar estas clases como servicios radica en que las clases que los usen no tienen que crear la instancia del servicio ni preocuparse de como les llegan, de esto se encarga una libreria del framework llamada `Inyector de Dependencias <http://es.wikipedia.org/wiki/Inyecci%C3%B3n_de_dependencias>`_.

Ademas los servicios tambien pueden depender de otros servicios, entonces el inyector al crear las instancias de los primeros les proporciona las instancias que estos necesitan.

Por ultimo es importante destacar que los servicios que se crean son instancias unicas que se mantienen desde que se crean hasta terminar la petición, es decir, si en algun momento necesitamos de un servicio, el inyector primero verifica si ya habia sido previamente creado, si es así, simplemente lo devuelve a quien lo solicitó, de no existir el servicio aun, lo crea y lo guarda junto con los demas servicio creados por si mas adelante se vuelve a solicitar.

.. contents:: Índice:

Definiendo un Servicio
----------------------

Los servicios se definen en un archivo llamado services.ini que se puede encontrar en "proyecto/app/config/services.ini" y/ó dentro de la carpeta config de cada módulo. Ejemplos:

  * app/config/services.ini                           archivo services global de la App
  * app/moudles/K2/Backend/config/services.ini        archivo services del módulo K2/Backend
  * app/modules/Index/config/services.ini             archivo services del módulo Index

En cada uno de esos archivos se pueden definir servicios que luego serán creados por el framework solo si son necesitados.

Se debe tener cuidado al nombrar los servicios, ya que si 2 servicios tienen el mismo nombre, el ultimo en ser leido por el framework en los services.ini va a ser el que se creé realmente.

Nombre del Servicio
___________________

el nombre del servicio es cualquier cadena valida, no debe llevar espacios en blanco ni caracteres especiales, solo...

Estableciendo Dependencias
--------------------------

Escuchando Eventos
------------------