KumbiaPHP 2
===========

Esta es una base para una nueva versión de KumbiaPHP framework, trabaja con php 5.3 ó superior.

    * Estado del Core: |buildcore|
    * Estado del ActiveRecord: |buildar|

.. |buildcore| image:: https://secure.travis-ci.org/manuelj555/Core.png?branch=master
.. |buildar| image:: https://secure.travis-ci.org/manuelj555/activerecord.png?branch=php5.3

`Documentación <https://github.com/manuelj555/k2/tree/master/doc/README.rst>`_

Instalación
-----------

KumbiaPHP 2 es muy facil de instalar, solo debes ir a la sección de descargas y bajar la ultima versión del proyecto desde aquí (Descargar el .zip con la fecha mas reciente):

`Descargas <https://github.com/manuelj555/k2/downloads>`_

Instalación mediante Composer
_____________________________

Ademas se puede realizar la instalación a traves de `composer <https://github.com/composer/composer>`_, se debe de descargar el `composer.phar <https://getcomposer.org/composer.phar>`_ ó ejecutar el siguente comando en la raiz del proyecto:
::

    curl -s https://getcomposer.org/installer | php

Luego de tener descargado el archivo composer.phar, procedemos a ejecutarlo para instalar las dependencias:
::

     php composer.phar install

Este comando instalará todas las dependencias necesarios del framework, ademas podemos agregar dependencias a librerias que necesitemos en proyectos especificos.

Requerimientos
--------------

Esta esta versión necesita PHP 5.3.* en adelante para trabajar, ya que se incorporan los namespaces que ofrecen las nuevas versiones de php.

Ademas se necesita tener activado el `mod_rewrite <https://www.google.com/search?q=mod_rewrite>`_ de Apache para poder trabajar las URL.

Para las conexiones a BD se utiliza PDO por lo que es necesario tener activada dicha extensión.

Estos son los tres requermientos básicos para poder Trabajar con KumbiaPHP 2.



