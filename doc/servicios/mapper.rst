Servicio mapper
===========

EL servico mapper, ó mapeador permite pasar datos de un arreglo a un objeto, donde los indices del arreglo se guardarán en los atributos del objeto que tengan el mismo nombre, ya sea acceciendo directamente a los atributos si son publicos, ó mediante métodos getters si los atributos no pueden ser accedidos publicamente.

Muy util cuando queremos pasar valores de la variable $_POST por ejemplo, ó de algún array que provenga de cualquier otra parte.

Además ofrece una interfaz mediante la cual podremos filtrar la data que se le pasará a las instancias de las clases que implementen `K2\\DataMapper\\MapperInterface <https://github.com/k2framework/Core/blob/master/src/K2/DataMapper/MapperInterface.php>`_ gracias a la clase `K2\\DataMapper\\MapperBuilder <https://github.com/k2framework/Core/blob/master/src/K2/DataMapper/MapperBuilder.php>`_

