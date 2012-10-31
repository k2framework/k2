Los Servicios
=============

Los Servicios no son más que clases php que realizan y ofrecen funcionalidades especificas dentro de una aplicación, por ejemplo una clase para envio de correos, una clase para generar Pdfs, para cache, logs, sesión, validación, etc.

La gran ventaja de usar estas clases como servicios radica en que las clases que los usen no tienen que crear la instancia del servicio ni preocuparse de como les llegan, de esto se encarga una libreria del framework llamada `Inyector de Dependencias <http://es.wikipedia.org/wiki/Inyecci%C3%B3n_de_dependencias>`_.

Ademas los servicios tambien pueden depender de otros servicios, entonces el inyector al crear las instancias de los primeros les proporciona las instancias que estos necesitan.

Por ultimo es importante destacar que los servicios que se crean son instancias unicas que se mantienen desde que se crean hasta terminar la petición, es decir, si en algun momento necesitamos de un servicio, el inyector primero verifica si ya habia sido previamente creado, si es así, simplemente lo devuelve a quien lo solicitó, de no existir el servicio aun, lo crea y lo guarda junto con los demas servicio creados por si mas adelante se vuelve a solicitar.

.. contents:: Índice:

Definiendo un Servicio
----------------------

Los servicios se definen en un archivo llamado services.ini que se puede encontrar en "proyecto/app/config/services.ini" y/ó dentro de la carpeta config de cada módulo. Ejemplos:

    * app/config/services.ini                           archivo services global de la App
    * app/moudles/K2/Backend/config/services.ini        archivo services del módulo K2/Backend
    * app/modules/Index/config/services.ini             archivo services del módulo Index

En cada uno de esos archivos se pueden definir servicios que luego serán creados por el framework solo si son necesitados.

Se debe tener cuidado al nombrar los servicios, ya que si 2 servicios tienen el mismo nombre, el ultimo en ser leido por el framework en los services.ini va a ser el que se creé realmente.

Nombre del Servicio
___________________

El nombre del servicio puede ser cualquier cadena válida, no debe llevar espacios en blanco ni caracteres especiales, solo guiones, puntos y/ó underescores, cada servicio representa una sección dentro del archivo services.ini y los pares clave valor dentro de estas secciones son las configuraciónes de cada servicio. Algunos ejemplos son:

    * [mi_servicio]
    * [otro-servicio]
    * [twitter]
    * [session]
    * [app.context]
    * [view]
    * [PHPExcel]

Propiedades de la Sección
_________________________

+------------------------------+-------------------------------------------------------------------------------------+
|**class** (obligatorio)       | clase que será creada, ejemplo::                                                    |
|                              |                                                                                     |
|                              |    class = KumbiaPHP\Kernel\Session\Session                                         |
+------------------------------+-------------------------------------------------------------------------------------+
|**construct[]** (opcional)    | parametros a pasar al servicio en el constructor, ejemplo::                         |
|                              |                                                                                     |
|                              |    ;pasando 1 solo parametro al constructor:                                        |
|                              |    construct = @app.context ;si solo es un parametro obviamos los corchetes         |
|                              |                                                                                     |           
|                              |    //pasando más de un parametro al constructor:                                    |  
|                              |    construct[] = @app.context ;esperamos un primer parametro                        | 
|                              |    construct[] = @session     ;esperamos un segundo parametro.                      |
|                              |    construct[] = config.name  ;esperamos un parametro de configuración llamado      |
|                              |                               config.name                                           |
+------------------------------+-------------------------------------------------------------------------------------+
|                              |                                                                                     |
|**call[metodo]** (opcional)   | metodo a llamar para insertar un servicio ó parametro, ejemplo::                    |
|                              |                                                                                     |
|                              |     call[setSession] = @session ;espera la instancia de la sesion en el método      |              
|                              |                                  setSession().                                      | 
|                              |     call[setAppContext] = @app.context ;espera la instancia de AppContext.          |
|                              |     call[setAppName] = config.name ;espera un parametro de config con el nombre     |
|                              |                                     de la aplicación.                               |
+------------------------------+-------------------------------------------------------------------------------------+
|**factory[method]**           | método estatico a llamar, el cual crea una instancia que es la que se guardará en   |
|y **factory[argument]**       | el inyector de dependencias. por ejemplo::                                          |
|(opcionales)                  |                                                                                     |
|                              |     factory[method]   = factory      ;llama al método estático llamdo factory() de  |
|                              |                                       la clase                                      |
|                              |     factory[argument] = @app.context ;se le pasa el sercivio AppContext como        |
|                              |                                       argumento al método factory()                 |
+------------------------------+-------------------------------------------------------------------------------------+
|**listen[metodo]** (opcional) |  se especifica un método que escuchará un evento del framework. Ejemplos::          |
|                              |                                                                                     |
|                              |      listen[onRequest] = kumbia.request ;se llama al método onRequest() en el       |
|                              |                                          evento kumbia.request                      |
|                              |      listen[miMetodo] = kumbia.response ;ejecutado en el evento kumbia.response     |
|                              |      listen[onError] = kumbia.exception ;ejecutado al ocurrir una excepcion         |
+------------------------------+-------------------------------------------------------------------------------------+

Ejemplos de Definiciones de Servicios:
______________________________________

::

   [session]
   class = KumbiaPHP\Kernel\Session\Session
   construct = @request ;el servicio @session usa el servicio @request
   
   [router]
   class =  KumbiaPHP\Kernel\Router\Router
   construct[] = @app.context ;el servicio @router usa el servicio @app.context
   construct[] = @app.kernel  ;el servicio @router usa el servicio @kernel
   
   [view]
   class = KumbiaPHP\View\View
   construct[] = @container ;el servicio @view usa el servicio @container
   
   [cache]
   class = KumbiaPHP\Cache\Cache
   factory[method] = factory   ;se llamará al método estático factory()
   factory[argument] = app_dir ;y se le pasará como parametro el valor del parametro app_dir
   
   [flash]
   class = KumbiaPHP\Flash\Flash
   construct[] = @session ;el servicio @flash usa el servicio @session
   
   [validator]
   class = KumbiaPHP\Validation\Validator  ;no usa otros servicios
   
   [security]
   class = KumbiaPHP\Security\Security
   construct[] = @session
   
   [activerecord.provider]
   class = KumbiaPHP\Security\Auth\Provider\ActiveRecord
   construct[] = @container

Estableciendo Dependencias
--------------------------

Algunos servicios (clases) necesitan de otros servicios ( otras clases ) para realizar algunas tareas especificas, por ejemplo el servicio para crear mensajes Flash necesita del servicio @session para guardar los mensajes entre una petición y otra, el servicio @router necesita dos servicios: el @app.context y el @app.kernel para poder trabajar con las redirecciónes dentro de la aplicación. Todo esto quiere decir que algunos servicios **dependen** de otros para su correcto funcionamiento.

Podemos lograr que a un servicio le lleguen las instancias de los servicios que necesitan mediante métodos de la clase ó desde el mismo constructor. Pero para lograr esto debemos configurarlo en nuestro archivo services.ini, en donde hallamos colocado la definición del servicio. Esto se logra de la siguiente manera:

::

   //codigo en services.ini
   [api.twitter]
   class = K2\Twitter\Twitter
   construct[] = @request ;el servicio @apt.twitter usa el servicio @request y le llegará en el constructor
   call[establecerSession] = @session ;se le pasa el servicio @session por medio del método establecerSession()
   call[setFlash]          = @flash   ;se le pasa el servicio @flash por medio del método setFlash()

   //servicio @Twitter

   namespace K2\Twitter\Twitter;

   class Twitter
   {
      protected $session;
      protected $flash;
      protected $request;

      public function __construct(Request $r) //acá estamos esperando la instancia del servicio @request.
      {
         //al solicitar la instancia del servicio @api.twitter, el inyector de dependencias le pasará a esta clase
         //el servicio @request en el constructor.
         $this->request = $r;
      }

      public function establecerSession(Session $session) //acá estamos esperando la instancia del servicio @session.
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

::

   //codigo en services.ini
   [flash]
   class = KumbiaPHP\Flash\Flash
   construct[] = @session ;el servicio @flash usa el servicio @session y le llegará en el constructor

   //servicio @flash

   namespace KumbiaPHP\Flash\Flash; 

   class Flash
   {
      protected $session;

      public function __construct(Session $session) //acá estamos esperando la instancia del servicio @session.
      {
         //al solicitar la instancia del servicio @flash, el inyector de dependencias le pasará a esta clase
         //el servicio session en el constructor.
         $this->session = $session;
      }
   }

::

   //codigo en services.ini
   [cache]
   class = KumbiaPHP\Cache\MiCache
   factory[method] = crearInstancia  ;se llamará a este método, el cual debe crear la instancia del servicio.
   factory[argument] = cache.driver  ;espera el valor contenido en el parametro de algun config.ini de la App.

   //servicio @MiCache

   namespace KumbiaPHP\Cache\MiCache;

   class MiCache
   {
      public static function crearInstancia($driver)
      {
         $driverClass = "KumbiaPHP\\Cache\\Adapter\\$driver"; creamos el nombre de la clase con el namespace.

         if ( !class_exist($driverClass) )  //si no existe la clase lanzamos una excepción.
         {
            throw new InvalidArgumentException("No existe el driver de cache $driver");
         }

         //si existe, creamos y retornamos la instancia del adaptador.
         return new $driverClass();
      }
   }

Escuchando Eventos
------------------