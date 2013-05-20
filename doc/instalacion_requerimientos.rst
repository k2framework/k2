Requerimientos de K2Framewrok
=============================

Esta versión necesita PHP 5.3.* en adelante para trabajar, ya que se incorporan los namespaces que ofrecen las nuevas versiones de php.

Ademas se necesita tener activado el `mod_rewrite <https://www.google.com/search?q=mod_rewrite>`_ de Apache para poder trabajar las URL.

Para las conexiones a BD se utiliza PDO por lo que es necesario tener activada dicha extensión.

Estos son los tres requermientos básicos para poder Trabajar con K2Framework.

Instalación
==========

K2Framework se descarga mediante composer, ó como un proyecto GIT:

Mediante Composer
--------

1. Descargar el proyecto como un .zip y descomprimir en el directorio public del servidor web
2. Descargar e instalar `composer <http://getcomposer.org/>`_
3. Colocarse en la carpeta del proyecto y mediante una consola de comandos ejecutar la siguiente instruccion:

::
    
    composer install
    
Mediante GIT
---------

Descargar como un proyecto GIT usando el siguiente comando:

::

    git clone --recursive git://github.com/k2framework/k2.git carpeta_proyecto
