El app/config/modules.php
============

.. contents:: El AppKernel es una clase que se encuentra en "proyecto/app/AppKernel.php", que nos permite registrar módulos y rutas en nuestra aplicación.

Codigo del AppKernel
--------------------

.. code-block:: php

    <?php
    require_once '../../vendor/autoload.php';
    
    use K2\Kernel\Kernel;
    
    class AppKernel extends Kernel
    {
    
        protected function registerModules()
        {
            $modules = array(
                new \Index\IndexModule(),
            );

            if (!$this->production) {
                $modules[] = new Demos\Modelos\ModelosModule();
                $modules[] = new Demos\Rest\RestModule();
                $modules[] = new Demos\Router\RouterModule();
                $modules[] = new Demos\SubiendoArchivos\ArchivosModule();
                $modules[] = new Demos\Seguridad\SeguridadModule();
                $modules[] = new Demos\Vistas\VistasModule();
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

    App::setLoader($loader);
    //acá podemos incluir rutas y prefijos al autoloader
    //$loader->add('K2\\Backend\\', __DIR__ . '/../../vendor/');

Como podemos ver este es un ejemplo del código que se encuentra en nuestro AppKernel.php, dicha clase tiene dos métodos principales "registerModules()" y "registerRoutes()", a traves de los cuales registraremos los módulos y libs que vayamos necesitando en la aplicación.

Para añadir libs solo es necesario registrarlas en el autoload de la aplicación, el cual está disponible mediante el uso del objeto $loader.


El Metodo registerModules()
-----------------------------

Este método permite registrar los modulos de la aplicación. por defecto carga el modulo Index y los demos que vienen con el proyecto.

Ejemplo
=======

.. code-block:: php

    protected function registerModules()
        {
            $modules = array(
                new \Index\IndexModule(),
                new \Namespace\MiModulo();
            );
            return $modules;
        }

Todo módulo debe tener una clase en la carpeta raiz del mismo que extienda de **K2\\Kernel\\Module** ya que será mediante dicha clase que registraremos el módulo en nuestro proyecto.

El Metodo registerRoutes()
-------------------------

A traves de este método registraremos los módulos que tendrá la aplicación, donde el índice del arreglo indica el prefijo inicial de la ruta que debe tener la URL para cargar el módulo y el valor de dicho indice será el nombre de nuestro módulo.

Prefijo de un Modulo
____________________

El prefijo de un módulo es la porción inicial de la URL, despues del PublicPath, que debe tener tener la misma para cargar un módulo especifico, veamoslo con algunos ejemplos:

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
  
::

    "/usuarios"        =>  "KumbiaPHP/Usuarios"
    "/clientes"        =>  "Index/Clientes"
    "/rest/carrito"    =>  "CarritoCompras"
    "/"                =>  "K2/Calendar"

Estos son ejemplos validos de prefijos asignados a módulos, se puede apreciar que no existe ninguna restricción en cuanto al nombre del prefijo y el nombre del módulo, estos pueden ser muy distintos unos de otros.
