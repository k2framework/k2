Formularios
===========

La lib para la creación de formularios nos permite de manera sencilla crear elementos (campos) del form, dandonos la seguridad de que solo esos campos y ningun otro data enviado por el cliente llegará a los modelos, ya que es en nuestros modelos de formulario ó en el contralador donde definiremos los campos que tendrá nuestro formulario y serán solos los valores de esos campos ( debidamente validados ), los que llegarán al modelo para ser procesados.

Creando un Formulario
---------------------

Veamos con un ejemplo como crear un formulario con tres campos, nombres, apellidos y edad:

.. code-block:: php

    //archivo app/modules/MiModulo/Controller/usuariosController.php

    namespace MiModulo\Controller;

    use K2\Form\Form;
    use K2\Kernel\Controller\Controller;

    class usuariosController extends Controller //ahora se extiende de una clase base Controller.
    {
        public function crear_action()
        {
            $form = new Form("nombre_form");//creamos la instancia del formulario y lo llamamos nombre_form

            $form->add("nombres") //por defecto add crea un campo de tipo texto si no lo especificamos.
                    ->setLabel("Escribe tus Nombres:") //agregamos un texto para el label
                    ->required(true); //le decimos que el campo es requerido (valida html5 y php)

            $form->add("apellidos", 'text') //acá le decimos explicitamente que el campo es de texto.
                    ->setLabel("Escribe tus Apellidos:")
                    ->required(); //le decimos que es requerido, por defecto es true si no pasamos nada

            $form->add("edad", "number") //agregamos un campo de tipo number.
                    ->setLabel("Escribe tu Edad:")
                    ->required()
                    ->range(18); //acá le decimos que el numero minimo es 18
                    //->range(18, 110); //acá le decimos que la edad es minimo 18 y máximo 110 años
                    //->range(18, 110, 'Tu edad debe estar entre {min} y {max}'); //acá pasamos un mensaje personalizado.

            $this->formulario = $form;//pasamos el objeto a la vista. 
        }
    }

    //la vista:

    <?php K2\View\View::content(true); //el true es para que muestre los mensajes flash ?>

    <?php echo $formulario;//imprimimos la variable y esto nos generará todo el formulario con los campos agregados. ?>

Como se puede apreciar es muy sencillo crear y agregar campos con la lib form, aparte esta puede renderizar todo el formulario sin nosotros tener que hacer nada especial (Con mensajes de error si el formulario es validado).

Personalizando el diseño del Formulario
---------------------------------------

La gran mayoria de la veces necesitaremos personalizar el como se muestran los campos de formulario, y realmente es muy sencillo lograr esto con la lib Form, veamos un ejemplo del mismo formulario anterior pero personalizado.

.. code-block:: html+php

    <?php K2\View\View::content(true); //el true es para que muestre los mensajes flash ?>

    <?php echo $formulario->open(); //crea la etiqueta de apertura. ?>

    <dl>

        <dd><label><?php echo $formulario['nombres']['label']; //imprime el label para el campo nombres ?></label></dd>
        <dt><?php echo $formulario['nombres']; //renderiza el campo de tipo texto nombres ?></dt>

        <dd><label><?php echo $formulario['apellidos']['label']; ?></label></dd>
        <dt><?php echo $formulario['nombres']; //renderiza el campo de tipo texto apellidos ?></dt>

        <dd><label><?php echo $formulario['edad']['label']; ?></label></dd>
        <dt><?php echo $formulario['edad']; //renderiza el campo edad (tipo number) ?></dt>

    </dl>

    <?php echo $formulario->close(); //crea la etiqueta de cierre. ?>

Como se puede apreciar es muy sencillo personalizar el formulario, ya que en la vista se trabaja como si fuese un arreglo del que mostramos los indices (estos indices son los campos agregados en el controlador). y solo con hacerles un echo, estos generan código html. Ademas, de una vez estos campos al ser requeridos tendrán el atributo required, y el campo edad tendrá ademas el atributo range para sus respectivas validaciones HTML5