<?php

use K2\Kernel\App;

########################### MODULOS ###################################

/* * *****************************************************************
 * Iinstalación de módulos
 */
App::modules(array(
    composerPath('k2/core', 'src/K2'),
    '/' => APP_PATH . 'modules/Index/config.php',
    include composerPath('k2/core', 'src/K2'),
    '/' => include APP_PATH . 'modules/Index/config.php',
));


/* * *****************************************************************
 * Agregamos módulos que solo funcionaran en desarrollo:
 */
if (false === PRODUCTION) {
    App::modules(array(
        composerPath('k2/debug', 'K2/Debug'),
        '/demo/vistas' => APP_PATH . 'modules/Demos/Vistas/config.php',
        '/demo/upload' => APP_PATH . 'modules/Demos/SubiendoArchivos/config.php',
        '/demo/router' => APP_PATH . 'modules/Demos/Router/config.php',
        '/demo/admin' => APP_PATH . 'modules/Demos/Seguridad/config.php',
        '/demo/rest' => APP_PATH . 'modules/Demos/Rest/config.php',
        '/demo/modelos' => APP_PATH . 'modules/Demos/Modelos/config.php',
    ));
}
