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
     * @param ServicioDependiente $sd 
     */
    public function __construct(ServicioDependiente $sd)
    {
        $this->sdsdd = $sd;
    }

    public function show($string)
    {
        echo '<p>', $string, '</p>';
    }

}