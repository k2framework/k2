<?php

namespace Index\Services;

use Index\Services\ServicioDependiente;

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
        echo $nombreApp;
    }

    public function show($string)
    {
        echo '<p>', $string, '</p>';
    }

}