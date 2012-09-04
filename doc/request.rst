El Objeto Request
=================

Este objeto es una capa de abstracción orientada a objetos de las variables globales de PHP, es decir, permite obtener valores de $_GET, $_POST, $_FILES, y $_SERVER por medio de una capa ( Clase Request ), orientada a objetos.

Las ventajas de esto son que evitamos tener que realizar tareas comunes que requieren un poco más de trabajo haciendolas a mano.

Como obtengo la instancia del Request actual
--------------------------------------------

En realidad esto es muy sencillo, en cualquier controlador que extienda de Controller, podemos llamar al método "getRequest()" y este nos devolverá la instancia del request actual, ejemplo:

::

    //archivo app/modules/MiModulo/Controller/UsuariosController.php
    <?php

    namespace MiModulo\\Controller;

    use KumbiaPHP\\Kernel\\Controller\\Controller;

    class UsuariosController extends Controller
    {
        public function index()
        {
            $request = $this->getRequest(); //este método nos devuelve la instancia del request actual.
            if ( $request->isAjax() ){
                ....
            }
        }
    }

Ahora, si tenemos un controlador que no extiende de Controller, y queremos obtener la instancia de Request, debemos tener como primer parametro en nuestra acción lo siguiente:

::

    //archivo app/modules/MiModulo/Controller/UsuariosController.php
    <?php

    namespace MiModulo\\Controller;

    class UsuariosController
    {
        public function index(Request $request)
        {
            //el framework al llamar a la acción index, le pasará la instancia de $request al parametro.
            $method = $request->getMethod(); 
        }
    }

