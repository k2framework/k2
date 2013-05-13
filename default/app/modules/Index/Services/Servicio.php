<?php

namespace Index\Services;

use K2\Kernel\Event\RequestEvent;
use K2\Validation\ValidationBuilder;
use K2\Security\Event\SecurityEvent;
use K2\Di\Container\Container;

/**
 * Description of Servicio
 *
 * @author manuel
 */
class Servicio
{

    /**
     *
     * @var Container 
     */
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function setSession(\K2\Kernel\Session\SessionInterface $sesion)
    {
        
    }

    public function onLogin(SecurityEvent $event)
    {
        $this->container->get('flash')->success("Bienvenido al sistema <b>{$event->getSecutiry()->getToken()->getUsername()}</b>");
    }

    public function cerrandoSesion(SecurityEvent $event)
    {
        $horas = date('H:i:s');
        $fecha = date('d-m-Y');
        $this->container->get('flash')->success("Sesión cerrada a las <b>{$horas}</b> Horas del Día <b>{$fecha}</b>");
    }

}