Servicio AppContext
===================

El servicio `app.context <https://github.com/manuelj555/Core/blob/master/src/KumbiaPHP/Kernel/AppContext.php>`_ nos ofrece una serie de métodos que nos permitiran obtener información de relevancia con respecto al contexto de la aplicación y la petición, ejemplo de ello es que podemos obtener el módulo actual en ejecución, el controlador, la acción, los parametros, la ruta hacia el módulo actual y hacia cualquier otro módulo, la ruta hacia la carpeta app del proyecto. Ademas nos permite crear urls para manejarnos dentro de la aplicación, entre otras cosas.

.. contents:: Contenido:

Metodos de la clase
-------------------

createUrl
__________

.. code-block:: php

    /**
     * Crea una url válida. todos las libs y helpers la usan.
     * 
     * Ejemplos:
     * 
     * $this->createUrl('admin/usuarios/perfil');
     * $this->createUrl('admin/roles');
     * $this->createUrl('admin/recursos/editar/2');
     * $this->createUrl('K2/Backend:usuarios'); módulo:controlador/accion/params
     * 
     * El ultimo ejemplo es una forma especial de crear rutas
     * donde especificamos el nombre del módulo en vez del prefijo.
     * ya que el prefijo lo podemos cambiar a nuestro antojo.
     * 
     * @param string $url
     * @param boolean $baseUrl indica si se devuelve con el baseUrl delante ó no
     * @return string
     * @throws NotFoundException si no existe el módulo
     */
    public function createUrl($url, $baseUrl = true)

getCurrentUrl
__________

.. code-block:: php

    /**
     * Devuelve la Url actual, completa, con módulo/controlador/acción
     * así estos no hayan sido especificados en la URL.
     * @param boolean $parameters si es true, agrega los parametros de la patición.
     * @return string 
     */
    public function getCurrentUrl($parameters = FALSE)

getControllerUrl
__________

.. code-block:: php

    /**
     * Devuelve la ruta hasta el controlador actual ejecutandose.
     * @param string $action si se especifica se añade al final de la URL
     * @return string 
     */
    public function getControllerUrl($action = null)

getCurrentModuleUrl
__________

.. code-block:: php

    /**
     * Devuulve el prefijo de la ruta que apunta al modulo actual.
     * @return string 
     */
    public function getCurrentModuleUrl()

setLocales
__________

.. code-block:: php

    public function setLocales($locales = null)

setRequestType
__________

.. code-block:: php

    /**
     * Establece el tipo de request del kernel, (MASTER, SUB)
     * @param string $type
     * @return \K2\Kernel\AppContext 
     */
    public function setRequestType($type)

getRequestType
__________

.. code-block:: php

    /**
     * Devuelve el tipo de request (MASTER, SUB)
     * @return string 
     */
    public function getRequestType()

getBaseUrl
__________

.. code-block:: php

    /**
     * Devuelve la url base del proyecto
     * @return string 
     */
    public function getBaseUrl()

getAppPath
__________

.. code-block:: php

    /**
     * Devuelve la ruta hacia la carpeta app
     * @return string 
     */
    public function getAppPath()

getRequestUrl
__________

.. code-block:: php

    /**
     * devuelve la url actual de la petición
     * @return string 
     */
    public function getRequestUrl()

getPath
__________

.. code-block:: php

    /**
     * Devuelve la ruta hacia la carpeta del módulo en cuestión.
     * @param string $module nombre del Módulo
     * @return null|string 
     */
    public function getPath($module)

getModules
__________

.. code-block:: php

    /**
     * devuelve los modulos registrados en el proyecto
     * @return array 
     */
    public function getModules($module = NULL)

getRoutes
__________

.. code-block:: php

    /**
     * devuelve las rutas registrados en el proyecto
     * @param string $route si se suministra un prefijo, devuelve solo
     * el valor de la ruta para ese prefijo.
     * @return array|string|NULL 
     */
    public function getRoutes($route = NULL)

getCurrentModule
__________

.. code-block:: php

    /**
     * Devuelve el prefijo actual del modulo que se está ejecutando
     * @return string 
     */
    public function getCurrentModule()

setCurrentModule
__________

.. code-block:: php

    /**
     * Establece el módulo actual en ejecucion
     * @param string $currentModule 
     * @return AppContext
     */
    public function setCurrentModule($currentModule)

getCurrentController
__________

.. code-block:: php

    /**
     * Devuelve el nombre del controlador actual en ejecución
     * @return string 
     */
    public function getCurrentController()

setCurrentController
__________

.. code-block:: php

    /**
     * Establece el nombre del controlador (en small_case) actual en ejecución
     * @param string $currentController 
     * @return AppContext
     */
    public function setCurrentController($currentController)

getCurrentAction
__________

.. code-block:: php

    /**
     * Devuelve el nombre de la accion actual (en small_case) en ejecución
     * @return string 
     */
    public function getCurrentAction()

setCurrentAction
__________

.. code-block:: php

    /**
     * Establece el nombre de la accion actual en ejecución
     * @param string $currentController
     * @return AppContext
     */
    public function setCurrentAction($currentAction)

getCurrentParameters
__________

.. code-block:: php

    /**
     * Devuelve los parametros de la petición.
     * @return array 
     */
    public function getCurrentParameters()

setCurrentParameters
__________

.. code-block:: php

    /**
     * Establece los parametros de la petición, enviados por la url
     * @param array $currentParameters
     * @return AppContext 
     */
    public function setCurrentParameters(array $currentParameters = array())

inProduction
__________

.. code-block:: php

    /**
     * devuelve TRUE si la app se encuentra en producción.
     * @return boolean 
     */
    public function InProduction()

setCurrentModuleUrl
__________

.. code-block:: php

    /**
     * Establece el prefijo de la url que identifica al modulo de la petición.
     * @param string $currentModuleUrl 
     * @return AppContext
     */
    public function setCurrentModuleUrl($currentModuleUrl)

parseUrl
__________

.. code-block:: php

    /**
     * Lee la Url de la petición actual, extrae el módulo/controlador/acción/parametros
     * y los almacena en los atributos de la clase.
     * @throws NotFoundException 
     */
    public function parseUrl()

Ejemplos
--------

.. code-block:: php

    //archivo app/modules/MiModulo/Controller/usuariosController.php
    
    namespace MiModulo\Controller;
    
    use K2\Kernel\Controller\Controller;
    
    class usuariosController extends Controller //ahora se extiende de una clase base Controller.
    {
        public function index_action()
        {
            $this->urlActual = $this->get('app.context')->getCurrentUrl(); //nos devuelve la url actual

            $this->urlHastaControlador = $this->get('app.context')->getControllerUrl(); //nos devuelve la url hasta el controlador actual

            //ahora crearemos una url hacia el módulo K2/Calendar, controlador eventosController acción agregar:
            $this->url = $this->get('app.context')->createUrl("K2/Calendar:eventos/agregar");
        } 
    }

.. code-block:: html+php

    //en una vista
    <?php use K2\View\View; ?>
    
    URL actual: <?php echo ::app()->getCurrentUrl(); //la lib view tiene una método llamado app(), que nos devuelve el servicio app.context ?>
    <a href="<?php echo View::app()->createUrl("K2/Calendar:eventos/agregar") ?>">Agregar Evento de Calendario</a>


