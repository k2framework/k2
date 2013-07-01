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
        'mi_servicio' => array(
            'callback' => function(Container $c) {
                return new Services\Servicio($c);
            },
            'tags' => array(
                array('name' => 'event.listener', 'event' => SE::LOGIN, 'method' => 'onLogin'), 
                array('name' => 'event.listener', 'event' => SE::LOGOUT, 'method' => 'cerrandoSesion'), 
            ),
        ),
    )
);


