Formularios
===========

Para la creación de formularios disponemos de varias funciones Twig:

    * **form_input(name, type = 'text', attrs = array(), value = null)**
    * **form_label(name, text, attrs = array())**
    * **form_textarea(name, attrs = array(), value = null)**
    * **form_check(name, value, attrs = array(), check = false)**
    * **form_radio(name, value, attrs = array(), check = false)**
    * **form_select(name, array options, attrs = array(), value = null)**
    * **form_choice(name, array options, multiple = true, attrs = array(), value = null)**
    * **form_options(array options, column, key = 'id')**

Estás funciones permiten crear de manera simple los elementos comunes presentes en cualquier formulario html.

Creando un Formulario
---------------------

Veamos con un ejemplo como crear un formulario con tres campos, nombres, apellidos y edad:

.. code-block:: html+jinja

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
