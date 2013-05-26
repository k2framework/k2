El config/modules.php
============

.. contents:: El modules.php es un archivo clase que se encuentra en "proyecto/app/config/modules.php", y que nos permite registrar módulos y rutas en nuestra aplicación.

Codigo del modules.php
--------------------

.. code-block:: php

    <?php

    use K2\Kernel\App;
    
    ########################### MODULOS ###################################
    
    /* * *****************************************************************
     * Iinstalación de módulos
     */
    App::modules(array(
        '/' => include APP_PATH . 'modules/Index/config.php',
        '/admin' => include composerPath('k2/backend', 'K2/Backend'),
        '/calendar' => include composerPath('k2/calendar', 'K2/Calendar'),
    ));
    
    
    /* * *****************************************************************
     * Agregamos módulos que solo funcionaran en desarrollo:
     */
    if (false === PRODUCTION) {
        App::modules(array(
            include composerPath('k2/debug', 'K2/Debug'),
            '/demo/vistas' => include APP_PATH . 'modules/Demos/Vistas/config.php',
            '/demo/upload' => include APP_PATH . 'modules/Demos/SubiendoArchivos/config.php',
            '/demo/router' => include APP_PATH . 'modules/Demos/Router/config.php',
            '/demo/admin' => include APP_PATH . 'modules/Demos/Seguridad/config.php',
            '/demo/rest' => include APP_PATH . 'modules/Demos/Rest/config.php',
            '/demo/modelos' => include APP_PATH . 'modules/Demos/Modelos/config.php',
        ));
    }


Como podemos ver este es un ejemplo del código que se encuentra en nuestro modules.php, dicha archivo hace uso de la clase K2\\Kernel\\App, a traves de la cual registramos los módulos que vayamos necesitando en la aplicación.

El Metodo App::modules()
-----------------------------

Este método permite registrar los modulos de la aplicación. recibe un array con la respuesta de la inclusión de los archivo config.php de cada módulo (dichos archivos deben retornar un array).

Ejemplo
=======

.. code-block:: php

    App::modules(array(
        '/' => include APP_PATH . 'modules/Index/config.php',
    ));
    
    App::modules(array(
        '/' => include APP_PATH . 'modules/Index/config.php',
        //si nuestro modulo fué instalador mediante composer, podemos registrarlo usando la funcion composerPath
        '/admin' => include composerPath('k2/backend', 'K2/Backend'),
    ));
    
    App::modules(array(
        //si nuestro módulo no será accesible desde el navegador, no le asignamos un indice
        include composerPath('k2/backend', 'K2/Backend'), 
    ));

Todo módulo debe tener un archivo php en la carpeta raiz del mismo, ya que será mediante este que registraremos el módulo en nuestro proyecto.
La funcion composerPath()
-------------------------

Esta función permite incluir módulos descargados mediante composer, y recibe 3 parametros

.. code-block:: php

    /**
     * Permite crear una ruta hasta un paquete instalado en vendor
     * @param string $package nombre del paquete, como se colocó en el composer.json
     * @param string $targetDir el target-dir usado por el paquete en su composer.json
     * @param string $file nombre del archivo php que contiene la configuración, por defecto config.php
     * @return string
     */
    function composerPath($package, $targetDir, $file = 'config.php')
    

Indice de un Modulo
____________________

El índice/prefijo de un módulo es la porción inicial de la URL, despues del PublicPath, que debe tener tener la misma para cargar un módulo especifico, veamoslo con algunos ejemplos:

Para llamar al "indexController" del módulo "Demos/Rest" nuestra URL de petición deberá comenzar por "/demo/rest", algunos patrones de URl que coincidiran con el prefijo son:

::

  /demo/rest                        // carga el controlador indexController y la acción index
  /demo/rest/                       // hace lo mismo que la ruta anterior
  /demo/rest/index                  // hace lo mismo que la ruta anterior
  /demo/rest/index/index            // hace lo mismo que la ruta anterior
  /demo/rest/index/otra_acción      // carga el controlador indexController y la acción otraAccion
  /demo/rest/ventas/crear           // carga el controlador ventasController y la acción crear
  
Ahora tenemos unos ejemplos de rutas que no concordarán con el prefijo /demo/rest

::

  /demo/restaurant                  // esta ruta no concuerda con el prefijo
  /demo/res/hola                    // esta ruta tampoco concuerda


Como debe ser el Prefijo
________________________

En realidad un prefijo puede ser cualquier patrón de url válido, y no necesariamente debe coincidir con el nombre del módulo, ejemplos de prefijos:
  
.. code-block:: php

    App::modules(array(
        '/' => include APP_PATH . 'modules/Index/config.php',
        '/admin' => include composerPath('k2/backend', 'K2/Backend'),
        '/demo/vistas' => include APP_PATH . 'modules/Demos/Vistas/config.php',
        '/demo/upload' => include APP_PATH . 'modules/Demos/SubiendoArchivos/config.php',
        '/demo/router' => include APP_PATH . 'modules/Demos/Router/config.php',
        '/demo/admin' => include APP_PATH . 'modules/Demos/Seguridad/config.php',
        '/demo/rest' => include APP_PATH . 'modules/Demos/Rest/config.php',
        '/demo/modelos' => include APP_PATH . 'modules/Demos/Modelos/config.php',
    ));

Estos son ejemplos validos de prefijos asignados a módulos, se puede apreciar que no existe ninguna restricción en cuanto al nombre del prefijo y el nombre del módulo, estos pueden ser muy distintos unos de otros.
