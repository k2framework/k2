El AppKernel
============

.. contents:: El AppKernel es una clase que se encuentra en "proyecto/app/AppKernel.php", que nos permite registrar módulos y rutas en nuestra aplicación.

Codigo del AppKernel
--------------------

.. code-block:: php

    <?php
    require_once '../../vendor/autoload.php';
    
    use KumbiaPHP\Kernel\Kernel;
    
    class AppKernel extends Kernel
    {
    
        protected function registerModules()
        {
            $modules = array(
                'KumbiaPHP'   => __DIR__ . '/../../vendor/kumbiaphp/core/src/',
                'Index'       => __DIR__ . '/modules/',
            );

            if (!$this->production) {
                $modules['Demos/Rest']              = __DIR__ . '/modules/';
                $modules['Demos/Router']            = __DIR__ . '/modules/';
                $modules['Demos/Vistas']            = __DIR__ . '/modules/';
                $modules['Demos/Modelos']           = __DIR__ . '/modules/';
                $modules['Demos/SubiendoArchivos']  = __DIR__ . '/modules/';
                $modules['Demos/Seguridad']         = __DIR__ . '/modules/';
            }

            return $modules;
        }

        protected function registerRoutes()
        {
            return array(
                '/'                 => 'Index',
                '/demo/rest'        => 'Demos/Rest',
                '/demo/router'      => 'Demos/Router',
                '/demo/vistas'      => 'Demos/Vistas',
                '/demo/modelos'     => 'Demos/Modelos',
                '/demo/upload'      => 'Demos/SubiendoArchivos',
                '/admin'            => 'Demos/Seguridad',
            );
        }
    
    }

Como podemos ver este es un ejemplo del código que se encuentra en nuestro AppKernel.php, dicha clase tiene dos métodos principales "registerModules()" y "registerRoutes()", a traves de los cuales registraremos los módulos y libs que vayamos necesitando en la aplicación.


El Metodo registerModules()
-----------------------------

Este método permite registrar los direcotiros donde se encuentran los modulos de la aplicación. por defecto carga el modulo KumbiaPHP en el dir dentro de vendor.

Si deseamos incluir alguna libreria que cumpla con el estandar autoload PSR-0, solo debemos instalarla en la carpeta vendors y registrarla en este método, donde el indice será el nombre de la libreria y el valor será la ruta hacia el direcotiro donde se encuentra la carpeta.


El Metodo registerRoutes()
-------------------------

A traves de este método registraremos los módulos que tendrá la aplicación, donde el índice del arreglo indica el prefijo inicial de la ruta que debe tener la URL para cargar el módulo y el valor de dicho indice será el nombre de nuestro módulo.

Prefijo de un Modulo
____________________

El prefijo de un módulo es la porción inicial de la URL, despues del PublicPath, que debe tener tener la la misma para cargar un módulo especifico, veamoslo con algunos ejemplos:

Para llamar al "IndexController" del módulo "Demos/Rest" nuestra URL de petición deberá comenzar por "/demo/rest", algunos patrones de URl que coincidiran con el prefijo son:

::

  /demo/rest                        // carga el controlador IndexController y la acción index
  /demo/rest/                       // hace lo mismo que la ruta anterior
  /demo/rest/index                  // hace lo mismo que la ruta anterior
  /demo/rest/index/index            // hace lo mismo que la ruta anterior
  /demo/rest/index/otra_acción      // carga el controlador IndexController y la acción otraAccion
  /demo/rest/ventas/crear           // carga el controlador VentasController y la acción crear
  
Ahora tenemos unos ejemplos de rutas que no concordarán con el prefijo /demo/rest

::

  /demo/restaurant                  // esta ruta no concuerda con el prefijo
  /demo/res/hola                    // esta ruta tampoco concuerda


Como debe ser el Prefijo
________________________

En realidad un prefijo puede ser cualquier patrón de url válido, y no necesariamente debe coincidir con el nombre del módulo, ejemplos de prefijos:
  
::

    "/usuarios"        =>  __DIR__ . "/modules/Admin/Usuarios/"
    "/clientes"        =>  __DIR__ . "/modules/MisClientes/"
    "/rest/carrito"    =>  __DIR__ . "/CarritoCompras/"
    "/"                =>  __DIR__ . "/modules/Home"

Estos son ejemplos validos de prefijos asignados a módulos, se puede apreciar que no existe ninguna restricción en cuanto al nombre del prefijo y el nombre del módulo, estos pueden ser muy distintos unos de otros.