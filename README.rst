K2 Framework PHP
===========

Framework que une la facilidad de trabajo de KumbiaPHP con el potencial de Symfony2, trabaja con php 5.3 ó superior.

    * Estado del Core: |buildcore|
    * Estado del ActiveRecord: |buildar|

.. |buildcore| image:: https://secure.travis-ci.org/k2framework/Core.png?branch=master
.. |buildar| image:: https://secure.travis-ci.org/k2framework/activerecord.png?branch=master

+ `Hola Mundo! <https://github.com/k2framework/k2/tree/master/doc/hola_mundo.md>`_
+ `Documentación <https://github.com/k2framework/k2/tree/master/doc/README.rst>`_

Instalación
-----------

La instalación es a traves de `composer <https://github.com/composer/composer>`_, se debe de descargar el `composer.phar <https://getcomposer.org/composer.phar>`_ ó ejecutar el siguente comando en la raiz del proyecto:
::

    curl -s https://getcomposer.org/installer | php

Luego de tener descargado el archivo composer.phar, procedemos a ejecutarlo para instalar las dependencias:
::

     php composer.phar install

Este comando instalará todas las dependencias necesarios del framework, ademas podemos agregar dependencias a librerias que necesitemos en proyectos especificos.

Requerimientos
--------------

Esta versión necesita PHP 5.3.* en adelante para trabajar, ya que se incorporan los namespaces que ofrecen las nuevas versiones de php.

Ademas se necesita tener activado el `mod_rewrite <https://www.google.com/search?q=mod_rewrite>`_ de Apache para poder trabajar las URL.

Para las conexiones a BD se utiliza PDO por lo que es necesario tener activada dicha extensión.

Estos son los tres requermientos básicos para poder Trabajar con K2.



