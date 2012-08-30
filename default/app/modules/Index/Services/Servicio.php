<?php

namespace Index\Services;

use KumbiaPHP\Kernel\Event\RequestEvent;
use KumbiaPHP\Validation\Validatable;
use KumbiaPHP\Validation\ValidationBuilder;

/**
 * Description of Servicio
 *
 * @author manuel
 */
class Servicio implements Validatable
{

    public function __construct(\KumbiaPHP\Kernel\AppContext $app)
    {
        //$this->show("dir App: " . $app->getAppPath());
    }
    
    public function setSession(\KumbiaPHP\Kernel\Session\SessionInterface $sesion){
        //var_dump($sesion);
    }

    public function show($string)
    {
        echo '<p>', $string, '</p>';
    }

    public function onRequest(RequestEvent $event)
    {
        //$this->show("Metodo Peticion: " . $event->getRequest()->getMethod());
    }

    public function buildValidations(ValidationBuilder $builder)
    {
        
    }

}