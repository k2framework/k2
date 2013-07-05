Los Servicios
=============

Los Servicios no son más que clases php que realizan y ofrecen funcionalidades especificas dentro de una aplicación, por ejemplo una clase para envio de correos, una clase para generar Pdfs, para cache, logs, sesión, validación, etc.

La gran ventaja de usar estas clases como servicios radica en que las clases que los usen no tienen que crear la instancia del servicio ni preocuparse de como les llegan, de esto se encarga una libreria del framework llamada `Inyector de Dependencias <http://es.wikipedia.org/wiki/Inyecci%C3%B3n_de_dependencias>`_.

Ademas los servicios tambien pueden depender de otros servicios, entonces el inyector al crear las instancias de los primeros les proporciona las instancias que estos necesitan.

Por ultimo es importante destacar que los servicios que se crean son instancias unicas que se mantienen desde que se crean hasta terminar la petición, es decir, si en algun momento necesitamos de un servicio, el inyector primero verifica si ya habia sido previamente creado, si es así, simplemente lo devuelve a quien lo solicitó, de no existir el servicio aun, lo crea y lo guarda junto con los demas servicio creados por si más adelante se vuelve a solicitar.

.. contents:: Índice:

Definiendo un Servicio
----------------------

Los Servicios se definen en los archivos config.php de cada módulo de la aplicación, veamos un ejemplo:

.. code-block:: php

    <?php
    
    namespace Index;
    
    use K2\Di\Container\Container;
    
    return array(
        'name' => 'Index',
        'namespace' => __NAMESPACE__,
        'path' => __DIR__,
        'services' => array(
            'pdf' => function(Container $c) {
                return new Lib\PDF($c);
            }
        )
    );


Acá hemos registrado un servicio al que llamamos "pdf", el cual representa la instancia de una clase Index\\Lib\\PDF, dicha clase solo será creada si en algun momento el servicio pdf es solicitado dentro de la aplicación.

Se debe tener cuidado al nombrar los servicios, ya que si 2 servicios tienen el mismo nombre, el ultimo en ser leido por el framework va a ser el que se creé realmente.

Nombre del Servicio
___________________

El nombre del servicio puede ser cualquier cadena válida, no debe llevar espacios en blanco ni caracteres especiales, solo guiones, puntos y/ó underescores, ejemplos

    * mi_servicio
    * otro-servicio
    * twitter
    * session
    * view
    * PHPExcel

Ejemplos de Definiciones de Servicios:
______________________________________

.. codeblock:: php

    <?php
    
    namespace Index;
    
    use K2\Di\Container\Container;
    
    return array(
        'name' => 'Index',
        'namespace' => __NAMESPACE__,
        'path' => __DIR__,
        'services' => array(
            'flash' => function() {
                return new K2\Flash\Flash();
            },
            'php_excel' => function() {
                return new K2\Excel\Excel();
            },
        )
    );

Estableciendo Dependencias
--------------------------

Algunos servicios (clases) necesitan de otros servicios ( otras clases ) para realizar algunas tareas especificas, por ejemplo el servicio para crear mensajes Flash necesita del servicio @session para guardar los mensajes entre una petición y otra. Todo esto quiere decir que algunos servicios **dependen** de otros para su correcto funcionamiento.

Podemos lograr que a un servicio le lleguen las instancias de los servicios que necesitan mediante métodos de la clase ó desde el mismo constructor, ejemplo:

.. code-block:: php

    <?php

   //servicio @Twitter

   namespace K2\Twitter;

   class Twitter
   {
      protected $session;
      protected $flash;

      public function __construct(Session $session) //acá estamos esperando la instancia del servicio @session.
      {
         //al solicitar la instancia del servicio @api.twitter, el inyector de dependencias le pasará a esta clase
         //el servicio session en el constructor.
         $this->session = $session;
      }

      public function setFlash(Flash $flash)
      {
         $this->flash = $flash;
      }
   }

Ahora en nuestro archivo config.php agregamos la definición del servicio:

.. code-block:: php

    <?php

    namespace Index;
    
    use K2\Di\Container\Container;
    
    return array(
        'name' => 'Index',
        'namespace' => __NAMESPACE__,
        'path' => __DIR__,
        'services' => array(
            'twitter' => function(Container $c) { //nuestra función siempre recibe el contenedor de servicios

                $twitter = new K2\Twitter\Twitter($c->get("session"));//le pasamos la instancia del servicio session

                $twitter->setFlash($c['flash']);//tambien podemos acceder a un servicio como si fuese un indice del container

                return $twitter;
            },
        )
    );

Podemos ver como hemos creado la instancia del objeto y luego le insertamos las dependencias, con lo cual, cuando solicitemos el servicio, este ya tendrá los objetos que le pasamos al crearlo.

Escuchando Eventos
------------------
Los servicios aparte de ofrecer una serie de métodos para la realización de las tareas que ofrece el mismo, pueden escuchar eventos despachados por el framework, es decir, pueden tener métodos que van a ser llamados por el kernel durante la ejecucion de eventos especificos en el recorrido de la patición ( evento request, eventos response, evento controller, evento exception, etc... ).

Esta posibilidad de que los servicios escuchen eventos, ofrece grandes oportunidades para la creación de funcionalidades adicionales a las que ofrece el framework por defecto, por Ejemplo:

    * Crear un servicio para enrutar las url.
    * Un servicio para manejo de seguridad.
    * Agregar contenido adicional a una respuesta.
    * Capturar las excepciones y generar una vista correspondiente.
    * LLevar una auditoria de las modificaciones de los datos en una BD.
    * Etc...

Como se puede apreciar son muchas las posibilidades que brinda el poder escuchar eventos en las aplicaciones.

Como escuchar un Evento
_______________________

Para que un servicio escuche eventos solo debemos agregalo al EventDispatcher en el config.php de nuestro módulo, ejemplo:

Crearemos un servicio llamado **k2_seguridad**, el cual escuchará el evento **k2.request**, entonces al iniciar la petición, se creará la instancia de la clase K2/Seguridad/Seguridad.php y se llamará al método verificarAcceso() de la misma, pasandole el objeto con la información del evento correspondiente, ejemplo del código de la clase:

.. code-block:: php

    //servicio @k2_seguridad

    namespace K2\Seguridad;

    use K2\Kernel\Event\RequestEvent;
    use K2\Kernel\Router\RouterInterface;

    class Seguridad
    {
        protected $router;

        public function __construct(RouterInterface $router){
            $this->router = $router; //establecemos la instancia del router
            
        }

        /**
         * Este método será llamado en la ejecución del evento k2.request.
         *
         * Es importante resaltar que el evento recibirá una instancia del objeto RequestEvent, el cual ofrece una serie de métodos
         * que nos permiten obtener data de relevancia para el evento en cuestion.
         * 
         * @param RequestEvent $event
         *
         */
        public function verificarAcceso(RequestEvent $event)
        {
            //verificamos si la ruta es segura llamando al método ficticio del ejemplo esRutaProtegida(), el cual
            //recibe la url actual de la petición.
            if ( $this->esRutaProtegida($event->getRequest()->getRequestUrl()) ){
                
                //si la ruta es segura verificamos si no ha iniciado session:
                if ( !$this->sesionIniciada() ){
                    //si aun no ha inicado sesion lo redirigimos al formulario
                    //establecemos una respuesta en el evento, para que no se ejecute el controlador.
                    $event->setResponse($this->router->redirect("login_url"));//lo enviamos a la página de login
                    $event->stopPropagation(); //ademas detenemos la ejecucion de eventos kumbia.request posteriores
                }
            }
        }
    }

Ahora agregamos el servicio al EventDispatcher:

.. code-block:: php

    <?php

    namespace K2\Seguridad;
    
    use K2\Di\Container\Container;
    
    return array(
        'name' => 'Index',
        'namespace' => __NAMESPACE__,
        'path' => __DIR__,
        'services' => array(
            'k2_seguridad' => array(
                'callback' => function($c) {
                    return new K2\Seguridad\Seguridad($c['router']);
                },
                'tags' => array(
                    array('name' => 'event.listener', 'event' => 'k2.request', 'method' => 'verificarAcceso')
                ),
            )
        ),
    );

El ejemplo aunque un poco complejo, ofrece una visión de lo que se puede lograr escuchando eventos en nuestras aplicaciones.

Ahora nuestro servicio k2_seguridad está escuchando varios eventos, veamos como sería el código de la clase:

.. code-block:: php

    //servicio @k2_seguridad

    namespace K2\Seguridad;

    use K2\Kernel\Event\RequestEvent;
    use K2\Kernel\Event\ResponseEvent;
    use K2\Kernel\Event\ExceptionEvent;

    class Seguridad
    {
        public function verificarAcceso(RequestEvent $event)
        {
            //codigo correspondiente
        }

        public function ocurrioExcepcion(ExceptionEvent $event)
        {
            //codigo correspondiente
        }

        public function onResponse(ResponseEvent $event)
        {
            //codigo correspondiente
        }
    }

La clase Seguridad tiene tres métodos que están escuchando diferentes eventos, y cada uno de ellos espera un tipo de objeto diferente que ofree métodos de utilidad para el tipo de evento.

En el config.php:

Ahora agregamos el servicio al EventDispatcher:

.. code-block:: php

    <?php

    namespace K2\Seguridad;
    
    use K2\Di\Container\Container;
    
    return array(
        'name' => 'Index',
        'namespace' => __NAMESPACE__,
        'path' => __DIR__,
        'services' => array(
            'k2_seguridad' => array(
                'callback' => function($c) {
                    return new K2\Seguridad\Seguridad($c['router']);
                },
                'tags' => array(
                    array('name' => 'event.listener', 'event' => 'k2.request', 'method' => 'verificarAcceso')
                    array('name' => 'event.listener', 'event' => 'k2.exception', 'method' => 'ocurrioExcepcion')
                    array('name' => 'event.listener', 'event' => 'k2.response', 'method' => 'onResponse')
                ),
            )
        ),
    );
