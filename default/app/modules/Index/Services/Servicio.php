<?php

namespace Index\Services;

use KumbiaPHP\Kernel\Event\RequestEvent;

/**
 * Description of Servicio
 *
 * @author manuel
 */
class Servicio
{

    public function __construct(\KumbiaPHP\Kernel\AppContext $app)
    {
        $this->show("dir App: " . $app->getAppPath());
    }

    public function show($string)
    {
        echo '<p>', $string, '</p>';
    }

    public function onRequest(RequestEvent $event)
    {
        $this->show("Metodo Peticion: " . $event->getRequest()->getMethod());
    }

}