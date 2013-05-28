Creando un Scaffold
===================
Creando el Módelo
-----------------
El scaffold trabaja con modelos que extienden del ActiveRecord de KumbiaPHP. por lo que debemos crear una clase que
extenderá del ActiveRecord y será nuestro modelo para el Scaffold.

.. code-block:: php

    <?php //archivo app/modules/Demos/Modelos/Model/Usuarios.php

    namespace Demos\Modelos\Model;

    use K2\ActiveRecord\ActiveRecord;

    class Usuarios extends ActiveRecord
    {

    }

Creando el Controlador
----------------------

El controlador es igual a cualquier controlador de esta nueva versión, `Ejemplo de un Controlador <https://github.com/k2framework/k2/blob/master/doc/controlador.rst#ejemplo-de-un-controlador>`_, la diferencia está en que debemos extender nuestro controlador de la clase **Scaffold\\Controller\\ScaffoldController**, y debemos implementar el método beforeFilter():

.. code-block:: php

    <?php //archivo app/modules/Demos/Modelos/Controller/indexController.php

    namespace Demos\Modelos\Controller;

    use Demos\Modelos\Model\Usuarios;
    use Scaffold\Controller\ScaffoldController;

    class indexController extends ScaffoldController
    {

        protected function beforeFilter()
        {
             //debemos crear y almacenar la instancia del módelo para el CRUD en el tributo $model
            $this->model = new Usuarios();
        }
    }

Es **importante** que en la implementación del **beforeFilter()** creemos y almacenemos la **instancia del modelo** para el scaffold en el atributo **$model** de la clase **ScaffoldController**. si no hacemos estó, una excepcion de tipo LogicException será lanzada.

Probando el CRUD
----------------

Para probar el crud solo basta con ir a la url del controlador y nos debe aparecer algo como esto:

.. image:: https://raw.github.com/k2framework/k2/master/doc/img/scaffold1.png
   :width: 600px
