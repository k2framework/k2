Hola Mundo en K2 Framework
=====================

Este tutorial servirá para instalar y crear la primera página usando K2.

Pasos:

  * Instalar [composer](http://getcomposer.org/)
  * Descargar [K2 Framework](https://github.com/k2framework/k2/archive/master.zip)
  * Instalar los vendors usando el comando ```composer install``` en la raiz del proyecto.

Con estos 3 primeros pasos ya debemos tener instalado y corriendo K2 en nuestro servidor web.

Podemos verificarlo accediendo desde el navegador a http://localhost/descarga_k2/

Nos debe aparecer una página de bienvenida como la siguiente:

   ![Bienvenida](https://raw.github.com/k2framework/k2/master/doc/img/bienvenida.png)
   
Si no es así verifica que se hayan instalado correctamente los paquetes mediante composer.

Creando una acción
--------------

Por defecto si no escribimos ninguna ruta en especifico en el navegador, se carga el módulo Index que se encuentra en **default/app/modules/Index**, se ejecuta el controlador **indexController** de dicho módulo y la acción **index_action()**, esto es así debido a la configuración inicial del framework en cuanto a la carga de módulos del archivo **default/app/config/modules.php**.

Vamos a editar el archivo **default/app/modules/Index/Controller/indexController.php** y le vamos a añadir un nuevo método que será nuestra acción que mostrará el Hola Mundo!.

```php
<?php

namespace Index\Controller;

use K2\Kernel\App;
use K2\Kernel\Controller\Controller;

class indexController extends Controller
{

    ...
    // creamos un nuevo método, que espera una parametro, y si no se pasa por defecto toma el string 'Mundo'
    public function hola_action($name = 'Mundo')
    {
        //creamos un atributo llamado nombre y le pasamos el contenido de $name, por lo que
        //ahora en la vista tendremos disponible una variable llamada nombre
        $this->nombre = $name;
    }

    ...

}
```

Nuestra acción puede recibir un parametro, y si no se le pasa ninguno, toma el valor por defecto.

El sufijo **_action** es muy importante, porque eso le indica al framework que nuestro método es una acción accesible desde el navegador.

la ruta para acceder a nuestra acción es: **http::/localhost/proyecto_k2/index/hola** pero cuidado que si lo hacemos ahora, el framework nos lanzará una excepción diciendonos que no existe la vista **default/app/modules/Index/View/index/hola.twig**

