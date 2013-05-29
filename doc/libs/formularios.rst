Formularios
===========

Para la creación de formularios disponemos de varias funciones Twig:

    * **form_*(name, attrs = array(), value = null)**
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

    {{ form_label('persona.nombres', 'Nombres') }}
    {{ form_text('persona.nombres') }} {# llama a la funcion form_* #}
    
    {{ form_label('persona.apellidos', 'Apellidos') }}
    {{ form_text('persona.apellidos') }}
    
    {{ form_label('persona.edad', 'Edad') }}
    {{ form_number('persona.edad', {min:1, max: 110}, 18) }} {# por defecto muestra 18 en la edad #}

Como se puede apreciar es muy sencillo crear y agregar campos con la lib form.

form_*()
---------

Permite crear campos de tipo text, hidden, password, number, email, url, color, etc...

Los atributos que acepta son:

    * **field**: nombre del input (genera name y id, convierte los puntos para el name en notación de array y para el id los separa con _).
    * **attrs**: un arreglo twig con los atributos para el input (class, style, required, disabled, ...)
    * **value**: valor inicial para el elemento, por defecto null.

.. code-block:: html+jinja

    {{ form_text('persona.nombres') }}    
    <!-- <input type="text" name="persona[nombres]" id="persona_nombres" /> -->
    
    {{ form_text('direccion') }}    
    <!-- <input type="text" name="direccion" id="direccion" /> -->
    
    {{ form_number('edad') }}    
    <!-- <input type="number" name="edad" id="edad" /> -->
    
    {{ form_color('user.color') }}    
    <!-- <input type="color" name="user[color]" id="user_color" /> -->
    
    {{ form_url('user.website', attrs={maxlength:120}) }}    
    <!-- <input type="url" name="user[website]" id="user_website" /> -->
    
    {{ form_email('user.correo') }}    
    <!-- <input type="email" name="user[correo]" id="user_correo" /> -->
        
    {{ form_password('clave') }}    
    <!-- <input type="password" name="clave" id="clave" /> -->
        
    {{ form_hidden('id', value="23") }}    
    <!-- <input type="hidden" name="id" id="id" value="23" /> -->
        
    {{ form_hidden('persona.id') }}
    <!-- <input type="hidden" name="persona[id]" id="persona_id" /> -->


form_label()
---------

Permite crear etiquetas label para los campos

Los atributos que acepta son:

    * **field**: nombre del input (genera atributo for, convierte los puntos en _).
    * **text:** texto a mostrar en el label.
    * **attrs**: un arreglo twig con los atributos para el input (class, style, ...)

.. code-block:: html+jinja

    {{ form_label('persona.nombres', 'Nombres') }}    
    <!-- <label for="persona_nombres">Nombres</label> -->
    
    {{ form_label('nombres', 'Nombres') }}    
    <!-- <label for="nombres">Nombres</label> -->
    
    {{ form_label('u.edad', 'Edad del Infante', {class:'form-label'}) }}    
    <!-- <label for="u_edad" class="form-label">Edad del Infante</label> -->
    

form_textarea()
---------

Permite crear campos textarea

Los atributos que acepta son:

    * **field**: nombre del input (genera name y id, convierte los puntos para el name en notación de array y para el id los separa con _).
    * **attrs**: un arreglo twig con los atributos para el input (class, style, required, disabled, ...)
    * **value**: valor inicial para el elemento, por defecto null.

.. code-block:: html+jinja

    {{ form_textarea('persona.nombres') }}    
    <!-- <textarea name="persona[nombres]" id="persona_nombres"></textarea> -->
    
    {{ form_input('direccion', value = objeto.campo) }}    
    <!-- <textarea name="direccion" id="direccion" >valor del campo</textarea> -->
    
form_radio()
---------

Permite crear campos de tipo radio

Los atributos que acepta son:

    * **field**: nombre del input (genera name y id, convierte los puntos para el name en notación de array y para el id los separa con _).
    * **value**: valor para el radio
    * **attrs**: un arreglo twig con los atributos para el input (class, style, required, disabled, ...)
    * **check**: indica si el campo aparecerá seleccionado o no.

.. code-block:: html+jinja

    {{ form_radio('persona.adulto', 1, check = true) }}    
    <!-- <input type="radio" name="persona[adulto]" id="persona_adulto" value="1" checked="checked" /> -->
    
    {{ form_radio('acepta_terminos', 'Si') }}    
    <!-- <input type="radio" name="direccion" id="direccion" value="Si" /> -->
    
    {{ form_radio('acepta_terminos', 'No') }}    
    <!-- <input type="radio" name="direccion" id="direccion" value="No" /> -->
    
    
form_checkbox()
---------

Cumple exactamente la misma función que form_radio, solo que genere inputs de tipo checkbox

form_select()
---------

Permite crear campos de tipo radio

Los atributos que acepta son:

    * **field:** nombre del input (genera name y id, convierte los puntos para el name en notación de array y para el id los separa con _).
    * **options:** arreglo con pares clave valor, donde la clave será el value de las opcionesy el valor el Texto a mostrar en las mismas.
    * **attrs:** un arreglo twig con los atributos para el input (class, style, required, disabled, ...)
    * **value:** valor inicial para el elemento, por defecto null.
    * **empty:** texto a mostrar inicialmente, por defecto es - seleccione -

.. code-block:: html+jinja

    {% set sexos = { 1 : 'Hombre' , 2 : 'Mujer' } %}

    {{ form_select('persona.sexo', sexos) }}    
    <!-- <select name="persona[sexo]" id="persona_sexo">
            <option>- Seleccione -</option>
            <option value="1" >Hombre</option>
            <option value="2" >Mujer</option>
         </select> -->

    {{ form_select('sexo', sexos, value=2) }}    
    <!-- <select name="sexo" id="sexo">
            <option>- Seleccione -</option>
            <option value="1" >Hombre</option>
            <option value="2" selected="selected" >Mujer</option>
         </select> -->
         
Ahora lo haremos con un array que viene de un php

.. code-block:: php

    <?php
    
    //algún método de un controlador
    public function crear_action()
    {
        $this->estatus = array(
        1 => "Activo",
        2 => "Inactivo",
        3 => "Removido",
        );
      
        $this->roles = Roles::createQuery()
                         ->select("id, nombre") //especificamos las dos columnas a traer
                         ->findAll(\PDO::FETCH_KEY_PAIR); //devuelve como array id => nombre
    }

En la vista:

.. code-block:: html+jinja

    {{ form_select('persona.status', status) }}  
    
    <!-- <select name="persona[status]" id="persona_status">
            <option>- Seleccione -</option>
            <option value="1" >Activo</option>
            <option value="2" >Inactivo</option>
            <option value="3" >Removido</option>
         </select> -->
         
    {{ form_select('persona.roles', roles) }}  
    
form_options()
---------

Permite crear un array con pares clave valor a partir de un array multidimensional ó un array de objetos. Es muy util cuando queremos pasar el resultado de una consulta a un select por ejemplo.

Los atributos que acepta son:

    * **options:** arreglo de arreglos u objetos que se van a leer.
    * **column:** nombre de la columna o atributo del objeto que se usara como el valor del arreglo que se devolverá.
    * **key:** nombre de la columna o atributo del objeto que se usara como clave del arreglo que se devolverá (por defecto busca id).
         
Tenemos una matriz y un array de objetos en un php

.. code-block:: php

    <?php
    
    //algún método de un controlador
    public function crear_action()
    {

       $this->estados = array(
           array('id' => 1, 'estado' => 'Aragua'),
           array('id' => 2, 'estado' => 'Carabobo'),
           array('id' => 3, 'estado' => 'Mérida'),
       );
       
       $this->roles = Model\Roles::findAll();
       $this->roles = Model\Roles::findAll('array'); //cualquier de las dos formas sirve :-)
       
       $this->bienFormado = Model\Roles::createQuery()
                                             ->select("id, nombre") //especificamos las dos columnas a traer
                                             ->findAll(\PDO::FETCH_KEY_PAIR);
                                             
       //este ultima consulta devolverá las opciones como un array con pares clave valor, donde la primera
       //columna del select serán los indices y la segundas los valores gracias al tipo de fetch:
       //          \PDO::FETCH_KEY_PAIR   una de las bondades de PDO :-)
       
    }


En la vista:

.. code-block:: html+jinja

    {% set estados_select = form_options(estados, 'estado') %} 
    {# crea un array donde las claves son los valores de la columna id de cada array de la matriz 
       y el valor es el contenido de la columna estado de cada elemento #}
    <!-- estados_select es igual a: {1:"Aragua", 2:"Carabobo", 3:"Mérida"}  -->
    
    {% set estados_select = form_options(estados, 'estado', 'id') %}
    {# igual al anterior, pero especificando la columna a usar para las keys #}

    {{ form_select('persona.estado', estados_select) }} {# le pasamos el nuevo array #}  

    {{ form_select('persona.estado', form_options(estados, 'estado')) }}{# llamamos directamente a la función #}  
    
    
    {{ form_select('persona.rol', form_options(roles, 'nombre')) }}{# llamamos directamente a la función #}  
    
    {{ form_select('user.roles', form_options(roles, 'nombre')),{multiple:true}}}
    
    Usando el array sin llamar a form_options
    {{ form_select('user.roles', bienFormado) }}
