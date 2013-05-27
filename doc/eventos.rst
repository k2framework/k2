Eventos
=======

En esta nueva versión de KumbiaPHP existe la posibilidad de escuchar y disparar eventos, lo que brinda la oportunidad de extender y/ó cambiar las funcionalidades ofrecidas por el framework, ya que podemos ejecutar tareas en determinados puntos de la aplicación y controlar el flujo de la misma, ya sea deteniendo la ejecución de la petición, cambiando el rumbo de esta ultima, cambiando el controlador, la acción ó la respuesta a mostrar, entre otras muchas posibilidades más.

.. contents:: Índice:

Eventos del Framework
---------------------
Evento kumbia.request
_____________________

El evento kumbia.request es ejecutado por el `kernel <https://github.com/k2framework/Core/blob/master/src/K2/Kernel/Kernel.php>`_ al inicio de la petición, despues de iniciar los parametros y objetos básicos que necesita la aplicación para funcionar.

Este evento ofrece a los escuchas un objeto de tipo `K2\\Kernel\\Event\\RequestEvent <https://github.com/k2framework/Core/blob/master/src/K2/Kernel/Event/RequestEvent.php>`_ mediante el cual podemos obtener el objeto Request, establecer el response, detener la ejecucion de los siguientes llamados a los escuchas, etc.

Estableciendo una Respuesta
...........................

Este evento ofrece la posibilidad de establecer una respuesta en el objeto RequestEvent, de hacerlo el kernel no creará la instancia del controlador, pasando directamente a ejecutar el evento kumbia.response ( saltandose el evento kumbia.controller ), para continuar con la ejecución de los procesos restantes, esto es util para evitar la ejecución del controlador en casos especiales como páginas seguras, etc. Ejemplo:

.. code-block:: php

    <?php

    namespace K2\Seguridad;

    use K2\Kernel\Response;

    class Seguridad
    {
        /**
         * Este método escucha el evento kumbia.request
         */
        public function verificarAcceso(RequestEvent $event)
        {
            if ( !$this->sesionIniciada() ){
                $event->setResponse(new Response("Acceso Denegado", 403));
                $event->stopPropagation();
            }
        }
    }

Al establecer una respuesta en el objeto $event, no se ejecutará el controlador ni el evento kumbia.controller

Evento kumbia.response
______________________
El evento kumbia.response es ejecutado por el `kernel <https://github.com/k2framework/Core/blob/master/src/K2/Kernel/Kernel.php>`_ despues de ejecutar el controlador, y contiene el objeto request y el objeto response con el contenido de la respuesta ya establecido.

Este evento ofrece a los escuchas un objeto de tipo `K2\\Kernel\\Event\\ResponseEvent <https://github.com/k2framework/Core/blob/master/src/K2/Kernel/Event/ResponseEvent.php>`_ mediante el cual podemos obtener el objeto Request, obtener la instancia de la respuesta, etc...

Generalmente es usado para cambiar el contenido de la respuesta ( agregar ó quitar partes, para agregar un debug, algun menú, etc... ).

Ejemplo de Uso
..............
.. code-block:: php

    <?php

    namespace K2\Debug\Service;

    class Debug
    {
        /**
         * Este método escucha el evento kumbia.response
         */
        public function onResponse(ResponseEvent $event)
        {
            if (!$this->request->isAjax()) {
                if (function_exists('mb_stripos')) {
                    $posrFunction = 'mb_strripos';
                    $substrFunction = 'mb_substr';
                } else {
                    $posrFunction = 'strripos';
                    $substrFunction = 'substr';
                }

                $response = $event->getResponse();
                $content = $response->getContent();

                if (false !== $pos = $posrFunction($content, '</body>')) {

                    $html = $this->view->render(array(
                                        'template' => 'K2/Debug:banner',
                                        'params' => array(
                                            'queries' => $this->session->all('k2_debug_queries'),
                                            'dumps' => $this->dumps,
                                        ),
                            ))->getContent();

                    $this->session->delete(null, 'k2_debug_queries');

                    $content = $substrFunction($content, 0, $pos) . $html . $substrFunction($content, $pos);
                    $response->setContent($content);
                }
            }
        }
    }

Esta clase lo que hace es insertar un banner al final de la página con información de la petición.

Evento kumbia.exception
_______________________
El evento kumbia.exception es ejecutado por el `kernel <https://github.com/k2framework/Core/blob/master/src/H2/Kernel/Kernel.php>`_ cuando ocurre una excepción en la aplicación y está no es capturada, ofrece la instancia del request y la instancia de la excepcion que se lanzó.

Este evento ofrece a los escuchas un objeto de tipo `K2\\Kernel\\Event\\ExceptionEvent <https://github.com/k2framework/Core/blob/master/src/K2/Kernel/Event/ExceptionEvent.php>`_ mediante el cual podemos obtener el objeto Request, obtener la instancia de la excepcion, establecer una respuesta a mostrar, etc...

Ejemplo de Uso
..............

.. code-block:: php

    <?php

    namespace K2\Backend\Service;

    use K2\Kernel\Event\ExceptionEvent;
    use K2\Di\Container\ContainerInterface;
    use K2\Security\Exception\UserNotAuthorizedException;

    class Excepcion
    {

        protected $container;

        public function __construct(ContainerInterface $container)
        {
            $this->container = $container;
        }

        /**
        * Método que captura las excepciones del Backend.
        * @param ExceptionEvent $event 
        */
        public function onException(ExceptionEvent $event)
        {
            if ($event->getException() instanceof UserNotAuthorizedException) {
                $url = $event->getRequest()->getRequestUrl();
                $response = $this->container->get('view')
                        ->render(array(
                                'template' => 'K2/Backend:exception',
                                'params' => compact('url')
                        ));
                $event->setResponse($response);
            }
        }

    }

Este escucha del evento exception lo que hace es mostrar una página indicando que el usuario no tiene acceso a una parte de la aplicación.

Evento activerecord.beforequery
_______________________________
El evento activerecord.beforequery es ejecutado por el `ActiveRecord <https://github.com/k2framework/Core/blob/master/src/K2/ActiveRecord/PDOStatement.php#L33>`_ antes de ejecutar una consuta SQL, y contiene la cadena sql y los parametros de la misma (ya que son consultas preparadas).

Este evento ofrece a los escuchas un objeto de tipo `K2\\ActiveRecord\\Event\\BeforeQueryEvent <https://github.com/k2framework/Core/blob/master/src/K2/ActiveRecord/Event/BeforeQueryEvent.php>`_ mediante el cual podemos obtener el SQL que se va a ejecutar, obtener/editar los parametros que se enviaran en la consulta y el tipo de consulta a ejecutar (SELECT, INSERT, UPDATE, DELETE).

Evento activerecord.afterquery
______________________________
El evento activerecord.afterquery es ejecutado por el `ActiveRecord <https://github.com/k2framework/Core/blob/master/src/K2/ActiveRecord/PDOStatement.php#L41>`_ despues de ejecutar una consuta SQL, y contiene la cadena sql, los parametros de la misma (ya que son consultas preparadas), el objeto PDOStatement y el resultado del llamado al método `execute de la clase PDOStatement <http://php.net/manual/es/pdostatement.execute.php>`_.

Este evento ofrece a los escuchas un objeto de tipo `K2\\ActiveRecord\\Event\\AfterQueryEvent <https://github.com/k2framework/Core/blob/master/src/K2/ActiveRecord/Event/AfterQueryEvent.php>`_ mediante el cual podemos obtener el SQL que se ejecutó, obtener los parametros que se enviaron en la consulta, el tipo de consulta ejecutada (SELECT, INSERT, UPDATE, DELETE), el objeto PDOStatement y el resultado.

Ejemplo de Uso Before y After Query
...................................

.. code-block:: php

    <?php

    namespace K2\Debug\Service;

    use K2\Kernel\Request;
    use K2\Kernel\Event\ResponseEvent;
    use K2\Kernel\Session\SessionInterface;
    use K2\Di\Container\ContainerInterface;
    use K2\ActiveRecord\Event\AfterQueryEvent;
    use K2\ActiveRecord\Event\BeforeQueryEvent;

    class Debug
    {

        protected $queryTimeInit;

        protected $session;

        protected $request;

        protected $dumps;

        function __construct(ContainerInterface $container)
        {
            $this->view = $container->get('view');
            $this->session = $container->get('session');
            $this->request = $container->get('request');
        }
        
        /**
         * Este método escucha el evento activerecord.beforequery
         */
        public function onBeforeQuery(BeforeQueryEvent $event)
        {
            $this->queryTimeInit = microtime();
        }

        /**
         * Este método escucha el evento activerecord.afterquery
         */
        public function onAfterQuery(AfterQueryEvent $event)
        {
            if (!$this->request->isAjax()) {
                $this->addQuery($event, microtime() - $this->queryTimeInit);
            }
        }

        protected function addQuery(AfterQueryEvent $event, $runtime)
        {
            $data = array(
                'runtime' => $runtime,
                'query' => $event->getQuery(),
                'parameters' => $event->getParameters(),
                'type' => $event->getQueryType(),
                'result' => $event->getResult(),
            );
            $this->session->set(md5(microtime()), $data, 'k2_debug_queries');
        }

    }

El ejemplo anterior es un servicio que captura y va almaceando en un arreglo las consultas ejecutadas en la aplicaión, para luego mostrar los sql en la pantalla.
