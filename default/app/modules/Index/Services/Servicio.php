<?php

namespace Index\Services;

use Index\Services\ServicioDependiente;
use KumbiaPHP\Kernel\Event\RequestEvent;

/**
 * Description of Servicio
 *
 * @author manuel
 */
class Servicio
{

    /**
     * @Service(otro_servicio,$sd)
     * @Parameter(nombre_app,$nombreApp)
     * 
     * @param ServicioDependiente $sd 
     */
    public function __construct(ServicioDependiente $sd, $nombreApp)
    {
        $this->sdsdd = $sd;
    }

    public function show($string)
    {
        echo '<p>', $string, '</p>';
    }

    public function onKernelRequest(RequestEvent $event){
        //var_dump($event->getRequest()->getBaseUrl());
    }
}