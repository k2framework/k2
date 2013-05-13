<?php

namespace Demos\Vistas\Controller;

use K2\Kernel\Controller\Controller;

/**
 * Ejemplo de uso de las vistas en el nuevo FW
 *
 * @author manuel
 */
class indexController extends Controller
{

    public function index_action()
    {
        
    }

    public function saludo_action($nombre)
    {
        $this->tuNombre = $nombre;
        
        /*
         * Escogemos la vista otra_accion.phtml
         */
        $this->setView('@DemosVistas/index/otra_accion');
    }

}