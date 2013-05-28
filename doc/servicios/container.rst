Contenedor de Servicios
================

El contenedor de servicios de K2 no es una clase que se encarga de crear y administrar y mantener instancias de otras clases para que estás ultimas sean accesibles en toda la aplicación. Dichas clases son comunmente llamadas servicios, ya que ofrecen funcionalidades y caracteristicas a todo el framework.

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
