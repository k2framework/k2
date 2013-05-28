El servicio Validator
=====================

El servicio de validación llamado @validator nos ofrece una forma sencilla de validar objetos en nuestra aplicación, desde atributos no nulos, hasta atributos iguales, tamaños maximos y minimos de caracteres en los atributos, entre otras cosas.

Para lograr que un objeto pueda ser validado por al servicio **validator**, la clase que difine al objeto debe implementar la interfaz `K2\\Validation\\Validatable <https://github.com/k2framework/Core/blob/master/src/K2/Validation/Validatable.php>`_ la cual contiene tres métodos que deben ser implementados:

    * public function createValidations(ValidationBuilder $builder): en este método crearemos las reglas de validación usando el ValidationBuilder.
    * addError($index, $message): este método recibirá los errores que se presenten al validar.
    * getErrors(): este metodo retornará un arreglo con los errores de validación.

Cualquier clase que implemente la interfaz `K2\\Validation\\Validatable <https://github.com/k2framework/Core/blob/master/src/K2/Validation/Validatable.php>`_ puede ser validada por el servicio **validator**.

Validando un Objeto
-------------------

Veamos cómo validar un objeto con el servicio validator:

.. code-block:: php

   <?php

   namespace K2\Backend\Model;

   use K2\Validation\Validatable;
   use K2\Validation\ValidationBuilder;

   class Contacto implements Validatable //implementamos la Interfaz
   {
      protected $nombres;
      protected $fechaNac;
      protected $comentario;

      private $errores = array();

      //implementamos el método createValidations
      public function createValidations(ValidationBuilder $builder)
      {
         $builder->notNull('nombres', array('message' => 'Debe especificar un Nombre'));
         $builder->notNull('fechaNac', array('message' => 'Debe especificar un Correo'));
         $builder->notNull('comentario', array('message' => 'Debe especificar un Comentario'));

         $builder->date('fechaNac', array('message' => 'Escriba una Fecha de Nacimiento Válida'));
      }

      //implementamos el método addError()
      public function addError($field, $message)
      {
         $this->errores[] = $message;//vamos almacenando los errores
      }

      //implementamos el método getErrors()
      public function getErrors()
      {
         return $this->errores;//devolvemos los errores
      }
   }

   //en el controaldor:

   <?php 

    use K2\Kernel\App;

   class indexController extends Controller
   {

      public function contacto_action()
      {
         $contacto = new Contacto();

         $contacto->setData($this->getRequest()->post('form')); //le pasamos la data por un método ficticio.

         //obtenemos el servicio validator y llamamos a su método validate()
         if ( App::get('validator')->validate($contacto) ){
            //guardamos la data, la enviamos por correo, etc...
            App::get('flash')->success('La data fué procesada con exito');
         }else{
            //si hubo errores de validación, los mandamos al servicio flash
            App::get('flash')->error($contacto->getErrors());
      }
   }
