Contenedor de Servicios
================

Un contenedor de servicios no es más que una implementación del patron de inyección de dependencias, mediante el cual se gestiona la creación de las clases (llamadas servicios), y se pasan a otras clases que dependen de estas para su correcto funcionamiento, quitandole a estas ultimas la responsabilidad de crear dichas dependencias.

Las ventajas de esto es que podemos cambiar facilmente una dependencia por otra, y las clases que usen dicha dependencia no tendrán que ser modificadas en lo absoluto.

El contenedor de servicios de K2 es una clase que se encarga de crear y administrar y mantener instancias de otras clases para que estás ultimas sean accesibles en toda la aplicación. Dichas clases son comunmente llamadas servicios, ya que ofrecen funcionalidades y caracteristicas a todo el framework.

Usando el Container
-------------

Para obtener y usar usar el container lo hacemos mediante la clase **K2\\Kernel\\App**, la cual nos ofrece un método estático que nos devuelve servicios disponibles en el container, y como el container a su vez es un servicio, podemos solicitarlo a el, ejemplo:

.. code-block:: php

    <?php
    
    use K2\Kernel\App;
    
    $contenedor = App::get("container"); //obtenemos la instancia del contenedor de servicios.
    
    //Ahora podemos acceder a los métodos del mismo:
    
    $flash = $contenedor->get("flash"); //nos devuelve la instancia del servicio flash.
    
    $flash->success("Enviando un mensaje de exito!");
    $contenedor->get("flash")->success("Enviando un mensaje de exito!");
    $contenedor["flash"]->success("Enviando un mensaje de exito!");
    App::get("flash")->success("Enviando un mensaje de exito!");
    
    //todas las formas anteriores son correctas.
    
Generalmente y para mayor comodidad usaremos la llamada a **App::get()** para obtener los servicios.

Registrando un servicio en el container
-------------------

Para registrar servicios el container ofrece un método set():

.. code-block:: php

    /**
     * Crea ó Actualiza la configuración para la creación de un servicio.
     * 
     * @example $container->set("session", function($c){
     *      return new K2\Kernel\Session\Session($c['request']);
     * });
     * 
     * @param string $id identificador del servicio
     * @param \Closure funcion que crea el servicio
     * @param boolean $singleton Indica si se va a mentener una sola instancia de la clase
     * ó se creará una nueva cada vez que el servicio sea solicitado
     */
    public function set($id, \Closure $function, $singleton = true)
    
Sin embargo muy pocas veces registraremos servicios usando directamente el container, K2 ofrece una forma simple de hacerlo en la configuracion de los módulos: `Creando un Servicio <https://github.com/k2framework/k2/blob/master/doc/servicios.rst#definiendo-un-servicio>`_

Estos son algunos ejemplos de uso:

.. code-block:: php

    <?php
    
    use K2\Kernel\App;
    
    $container = App::get("container");
    
    $container->set("mi_servicio", function($container){
        
        return new MiServicio($container->get("ssssion")); //devolvemos un objeto, que usa el servicio session
        
    });
    
    $container->set("otro_servicio", function($container){
        
        $mail = new Mailer($container->getParameter("mailer_config"));//creamos la instancia del servicio y le pasamos unos parametros que solicita.
        
        $mail->setTwig($container->get("twig")); //le pasamos la instancia del servicio twig, ya que la necesita.
        
        return $mail;//devolvemos la instancia creada.
        
    });
    
Registrando una instancia en el container
------------------

Aveces necesitamos registrar una instancia ya creada en el contenedor, esto lo podemos hacer mediante el método:

.. code-block:: php

    /**
     * Establece una instancia de un objeto en el indice especificado
     * @param string $id indice
     * @param object $object objeto a almacenar
     */
    public function setInstance($id, $object)
    
    //ejemplo:
    
    $user = Usuarios::findById(5);
    
    App::get("container")->setInstance("user_logged", $user);
    
Leyendo y escribiendo parametros en el Contenedor
-----------

El contenedor de servicios, aparte de contener las instancias de muchas de las clases del framework, contiene parametros de configuración de todo el sistema, los de los módulos, los del config.ini y los que agreguemos directamente en el container.

Leyendo parametros del contenedor:

.. code-block:: php

    <?php
    
    use K2\Kernel\App;
    
    $container = App::get("container");
    
    echo $container->getParameter("config.name"); //leemos el valor del indice name de la seccion [config] del config.ini
    echo App::getParameter("config.name"); //hace lo mismo que el código anterior.
    
    var_dump(App::getParameter("config")); devuelve un array con todos los valores del [config] en el config.ini
    
    //es posible acceder a indices de un array en el config usando el  punto **.** como separador, ejemplo:
    
    $container->setParameter("user", array( 'id' => 15 , 'nombre' => 'Manuel' ));
    
    echo $container->getParameter("user.id");
    echo $container->getParameter("user.nombre");
    
    var_dump($container->getParameter("user"));
    
    $container->setParameter("user", array( 
        'id' => 15 , 
        'nombre' => 'Manuel',
        'roles' => array(
            'user' => array ( 'id' => 1 , 'name' => 'ROL_USUARIO' ),
            'admin' => array ( 'id' => 2 , 'name' => 'ROL_ADMIN' ),
        ),
    ));
    
    echo App::getParameter("user.nombre");
    echo App::getParameter("user.roles.user.name");
    echo App::getParameter("user.roles.admin.name"); //obtenemos el valor del indice name del rol admin en los roles.
    
    var_dump(App::getParameter("user.roles"));
    var_dump(App::getParameter("user.roles.admin"));
    
Generalmente los parametros serán establecidos en el archivo de configuración de cada módulo, en el indice parameters del mismo, allí definiremos todos los parametros necesarios para el correcto funcionamiento del módulo.
