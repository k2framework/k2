El AppKernel
============

.. contents:: El AppKernel es una clase que se encuentra en "proyecto/app/AppKernel.php", que nos permite registrar módulos y namespaces en nuestra aplicación.

Codigo del AppKernel
--------------------

::

    <?php
    require_once '../../vendor/autoload.php';
    
    use KumbiaPHP\Kernel\Kernel;
    
    class AppKernel extends Kernel
    {
    
        protected function registerNamespaces()
        {
            return array(
                'modules' => __DIR__ . '/modules/',
                'KumbiaPHP' => __DIR__ . '/../../vendor/kumbiaphp/kumbiaphp/src/',
            );
        }
    
        protected function registerRoutes()
        {
            $routes = array(
                '/' => __DIR__ . '/modules/Index/',
            );
    
            if (!$this->production) {
                $routes['/demo/rest']     = __DIR__ . '/modules/Demos/Rest/';
                $routes['/demo/router']   = __DIR__ . '/modules/Demos/Router/';
                $routes['/demo/vistas']   = __DIR__ . '/modules/Demos/Vistas/';
                $routes['/demo/modelos']  = __DIR__ . '/modules/Demos/Modelos/';
                $routes['/demo/upload']   = __DIR__ . '/modules/Demos/SubiendoArchivos/';
            }
            return $routes;
        }
    
    }

Como podemos ver este es un ejemplo del código que se encuentra en nuestro AppKernel.php, dicha clase tiene dos métodos principales "registerNamespaces()" y "registerRoutes()", a traves de los cuales registraremos los namespaces de libs y módulos que vayamos necesitando en la aplicación.


El Metodo registerNamespaces()
-----------------------------

Este método permite registrar los direcotiros donde el autoload del framework buscará las clases que necesitemos usar en la aplicación, por defecto cargar el namespace KumbiaPHP en el dir dentro de vendor, y el directorio por defecto de los módulos de la aplicación.

Si deseamos incluir alguna libreria que cumpla con el estandar autoload PSR-0, solo debemos instalarla en la carpeta vendors y registrarla en este método, donde el indice será el nombre inicial del Namespace de la Lib y el valor, será la ruta hacia el direcotiro donde se encuentra la carpeta.


El Metodo registerRoutes()
-------------------------

A traves de este método registraremos los módulos que tendrá la aplicación, donde el índice del arreglo indica el prefijo inicial de la ruta que debe tener la URL para cargar el módulo y el valor de dicho indice será la ruta en donde se encuentra nuestro módulo.

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