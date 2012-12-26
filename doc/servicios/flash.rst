El servicio Flash
==================

.. contents:: El servicio K2\Flash\Flash nos permite enviar mensajes desde un controlador a una vista, ya sea en la misma petición ó en la siguiente, lo cual es muy util cuando queremos informar al usuario de un evento ocurrido en algun proceso, tambien es muy usado para enviar un mensaje y redirigir a otra página, y en esta, mostrar dicho mensaje si existe.

Métodos del Servicio
--------------------

set()
____
.. code-block:: php

    /**
     * Establece un mensaje flash
     * 
     * @param string $type tipo del mensaje ( success, info , error, advertencia )
     * @param string $message  el mensaje a guardar.
     */
    public function set($type, $message)

has()
____
.. code-block:: php

    /**
     * Verifica la existencia de un mensaje en la clase, se debe pasar su tipo
     * @param string $type
     * @return boolean 
     */
    public function has($type)

get()
____
.. code-block:: php

    /**
     * Devuelve los mensajes que han sido previamente guardados para un tipo especifico, si existen.
     * 
     * antes de devolverlos, son borrados de la sesión.
     * 
     * @param string $type
     * @return array|NULL 
     */
    public function get($type)

getAll()
_______
.. code-block:: php

    /**
     * Devuelve todos los mensajes guardados previamente y los borra
     * de la session.
     * 
     * @return array arreglo donde los indices son el tipo de mensaje y el valor
     * es el contenido del mensaje. 
     */
    public function getAll()

success()
________
.. code-block:: php

    /**
     * Establece un mensaje de tipo success
     * @param string $message 
     */
    public function success($message)

info()
_____
.. code-block:: php

    /**
     * Establece un mensaje de tipo info
     * @param type $message 
     */
    public function info($message)

warning()
________
.. code-block:: php
    
    /**
     * Establece un mensaje de tipo warning
     * @param string $message 
     */
    public function warning($message)

error()
______
.. code-block:: php

    /**
     * Establece un mensaje de tipo error
     * @param string $message 
     */
    public function error($message)

Ejemplo de Uso
--------------

En el siguiente ejemplo enviaremos 1 mensaje de información desde un controlador.

.. code-block:: php

    <?php

    namespace MiModulo\Controller;

    use K2\Kernel\Controller\Controller;

    class usuariosController extends Controller
    {
        public function index_action()
        {
            $this->get("flash")->info("Lista de Usuarios Vacía...!!!");
        }
    }

.. code-block:: phtml

    <!-- en la vista leemos el flash -->

    <?php if (View::flash()->has("info")): //se puede obviar el if, ya que si no existe se muestra vacio ?>
        <?php foreach(View::flash()->get("info") => $msj): ?>
            <div class="info"><?php echo $msj ?></div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- tambien se pueden imprimir todos los mensajes: -->

    <?php foreach(View::flash()->getAll() as $type => $msjs): ?>
        <?php foreach($msjs => $msj): ?>
            <div class="<?php echo $type ?>"><?php echo $msj ?></div>
        <?php endforeach; ?>
    <?php endforeach; ?>

    <!-- Ó mas facil aun, podemos dejar que la libreria View imprima todos los mensajes por nosotros,
    solo debemos pasar un true al llamar al método View::content(true) de siempre. -->

    <?php View::content(true); //pasando true como parametro se imprimiran todos los mensajes flash enviados. ?>

