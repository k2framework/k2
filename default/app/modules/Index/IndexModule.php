<?php

namespace Index;

use K2\Kernel\Module;
use K2\Kernel\Event\K2Events as E;
use K2\Security\Event\Events as SE;

class IndexModule extends Module
{

    public function init()
    {
        //si nuestra app no tendrá seguridad podemos comentar el llamado
        //a firewallConfig
        $this->firewallConfig();
        
        //acá agregamos un servicio de ejemplo.
        $this->container->set('mi_servicio', function($c) {
                    return new Services\Servicio($c);
                });
                
        $this->dispatcher->addListener(SE::LOGIN,array('mi_servicio', 'onLogin'));
        $this->dispatcher->addListener(SE::LOGOUT,array('mi_servicio', 'cerrandoSesion'));
    }

    protected function firewallConfig()
    {
        //agregamos el servicio firewall al container
        $this->container->set('firewall', function($c) {
                    return new \K2\Security\Listener\Firewall($c);
                });
        //hacemos que el firewall escuche las peticiones
        $this->dispatcher->addListener(E::REQUEST, array('firewall', 'onKernelRequest'), 100);
    }

}
