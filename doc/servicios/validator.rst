El servicio Validator
=====================

El servicio de validación llamado @validator nos ofrece una forma sencilla de validar objetos en nuestra aplicación, desde atributos no nulos, hasta atributos iguales, tamaños maximos y minimos de caracteres en los atributos, entre otras cosas.

Para lograr que un objeto pueda ser validado por al servicio **validator**, la clase que difine al objeto debe implementar la interfaz `KumbiaPHP\\Validation\\Validatable <https://github.com/manuelj555/Core/blob/master/src/KumbiaPHP/Validation/Validatable.php>`_ la cual contiene tres métodos que deben ser implementados:

    * getValidations(): este método debe devolver una instancia de `KumbiaPHP\\Validation\\ValidationBuilder <https://github.com/manuelj555/Core/blob/master/src/KumbiaPHP/Validation/ValidationBuilder.php>`_ con las validaciones para la clase definidas.
    * addError($index, $message): este método recibirá los errores que se presenten al validar.
    * getErrors(): este metodo retornará un arreglo con los errores de validación.

Cualquier clase que implemente la interfaz `KumbiaPHP\\Validation\\Validatable <https://github.com/manuelj555/Core/blob/master/src/KumbiaPHP/Validation/Validatable.php>`_ puede ser validada por el servicio **validator**.