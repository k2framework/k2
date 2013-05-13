<?php

use K2\Kernel\App;

########################### MODULOS ###################################

/* * *****************************************************************
 * Iinstalación de módulos
 */
App::modules(array(
    '/' => require_once APP_PATH . '/modules/Index/config.php',
));


/* * *****************************************************************
 * Agregamos módulos que solo funcionaran en desarrollo:
 */
if (false === PRODUCTION) {
    App::modules(array(
        '/demo/vistas' => require_once APP_PATH . '/modules/Demos/Vistas/config.php',
        '/demo/upload' => require_once APP_PATH . '/modules/Demos/SubiendoArchivos/config.php',
        '/demo/router' => require_once APP_PATH . '/modules/Demos/Router/config.php',
        '/demo/admin' => require_once APP_PATH . '/modules/Demos/Seguridad/config.php',
        '/demo/rest' => require_once APP_PATH . '/modules/Demos/Rest/config.php',
    ));
}
