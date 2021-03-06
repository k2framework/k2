<?php

namespace Demos\Router\Controller;

use K2\Kernel\Controller\Controller;
use K2\Kernel\Response;

/**
 * Ejemplo de redirecciones usando el servicio router
 *
 * @author manuel
 */
class indexController extends Controller
{

    /**
     * este metodo redirecciona a "demo/router/index/accion2"
     * @return Response 
     */
    public function index_action()
    {
        return $this->getRouter()->redirect('@DemosRouter/index/accion2');
    }

    /**
     * este metodo redirecciona a "demo/router/index/accion3"
     * @return Response 
     */
    public function to_action_action()
    {
        return $this->getRouter()->toAction('accion3');
    }

    /**
     * este metodo redirecciona a "demo/router/index/accion4"
     * Esta es una redireccion interna, es decir la url no cambia
     * y tampoco se ejecuta una nueva petición.
     * 
     * @return Response 
     */
    public function forward_action()
    {
        return $this->getRouter()->forward('demo/router/index/accion4');
    }

    public function accion2_action()
    {
        return $this->getRespuesta(__METHOD__);
    }

    public function accion3_action()
    {
        return $this->getRespuesta(__METHOD__);
    }

    public function accion4_action()
    {

        return $this->getRespuesta(__METHOD__);
    }

    /**
     *
     * @param type $accion
     * @return \K2\Kernel\Response 
     */
    protected function getRespuesta($accion)
    {
        return $this->render('@DemosRouter/index/index', array(
                    'accionEjecutada' => $accion,
        ));
    }

}