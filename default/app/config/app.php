<?php

use K2\Kernel\App;

########################### MODULOS ###################################

/* * *****************************************************************
 * Iinstalación de módulos
 */
App::modules(array(
    '/' => include APP_PATH . '/modules/Index/config.php',
));


/* * *****************************************************************
 * Agregamos módulos que solo funcionaran en desarrollo:
 */
if (false === PRODUCTION) {
    App::modules(array(
        '/demo/vistas' => include APP_PATH . '/modules/Demos/Vistas/config.php',
        '/demo/upload' => include APP_PATH . '/modules/Demos/SubiendoArchivos/config.php',
        '/demo/router' => include APP_PATH . '/modules/Demos/Router/config.php',
        '/demo/admin' => include APP_PATH . '/modules/Demos/Seguridad/config.php',
        '/demo/rest' => include APP_PATH . '/modules/Demos/Rest/config.php',
        '/demo/modelos' => include APP_PATH . '/modules/Demos/Modelos/config.php',
    ));
}
