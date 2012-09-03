KumbiaPHP 2
===========

Esta es una base para una nueva versión de KumbiaPHP framework, trabaja con php 5.3 ó superior.

`Documentación <./doc/>`_


Instalación
-----------

KumbiaPHP 2 es muy facil de instalar, en esta nueva versión la carpeta del proyecto y el core están separados en 
repositorios individuales, con los que podras actualizar el core sin que se modifique tu proyecto.

Descagar el proyecto, abrir una consola en la raiz del proyecto y ejecutar el siguiente comando:

::

    curl -s http://getcomposer.org/installer | php

Este comando instalará composer en la carpeta del proyecto, al terminar de ejecutar el comando debes tener un 
archivo llamado composer.phar.

Luego debes ejecutar el comando:

::

    php composer.phar install

Este comando instalará las dependencias necesarias para tener el core de kumbiaphp listo para ejecutar tu proyecto.

Con estos sencillos pasos ya podrás ejecutar tu aplicación desde el navegador.