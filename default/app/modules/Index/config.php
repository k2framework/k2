<?php

namespace Index;

use K2\Di\Container\Container;
use K2\Kernel\Event\K2Events as E;
use K2\Security\Event\Events as SE;

return array(
    'name' => 'Index',
    'namespace' => __NAMESPACE__,
    'path' => __DIR__,
    'parameters' => array(
    ),
    'services' => array(
        'mi_servicio' => function(Container $c) {
            return new Services\Servicio($c);
        }
    ),
    'listeners' => array(
        SE::LOGIN => array(
            array('mi_servicio', 'onLogin')
        ),
        SE::LOGOUT => array(
            array('mi_servicio', 'cerrandoSesion')
        ),
    ),
);


