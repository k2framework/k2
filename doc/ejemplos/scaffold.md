Creando un Scaffold
===================
Creando el Módelo
-----------------
El scaffold trabaja con modelos que extienden del ActiveRecord de KumbiaPHP. por lo que debemos crear una clase que
extenderá del ActiveRecord y será nuestro modelo para el Scaffold.
Creando el Controlador
----------------------

El controlador es igual a cualquier controlador de esta nueva versión, [Ejemplo de un Controlador](https://github.com/manuelj555/k2/blob/master/doc/controlador.rst#ejemplo-de-un-controlador>), la diferencia está en que debemos extender nuestro controlador de la clase **Scaffold\Controller\ScaffoldController**, y debemos implementar el método beforeFiler():

```php
//archivo app/modules/K2/Backend/UsuariosController.php
<?php

namespace MiModulo\Controller;

use Scaffold\Controller\ScaffoldController;

class UsuariosController extends ScaffoldController
{

    protected function beforeFilter()
    {
        //debemos crear y almacenar la instancia del módelo para el CRUD en el tributo $model
        $this->model = new \K2\Backend\Model\Roles();
    }

}
```

Es **importante** que en la implementación del **beforeFilter()** creemos y almacenemos la **instancia del modelo** para el scaffold en el atributo **$model** de la clase **ScaffoldController**. si no hacemos estó, una excepcion de tipo LogicException será lanzada.

Probando el CRUD
----------------
