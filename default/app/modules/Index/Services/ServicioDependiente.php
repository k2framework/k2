<?php

namespace Index\Services;

use Index\Services\Servicio;
use KumbiaPHP\Kernel\Request;

/**
 * Description of ServicioDependiente
 *
 * @author manuel
 */
class ServicioDependiente
{

    /**
     *
     * @var Servicio 
     */
    protected $servicio;

    /**
     *
     * @var Request 
     */
    protected $request;

    /**
     * @Service(request,$request)
     * 
     * @param Request $request 
     */
    public function __construct(Request $request = NULL)
    {
        $this->request = $request;
    }

    /**
     * Con la siguiente anotacion le decimos al inyector de dependencia
     * que llame a esta funcion al crear este servicio y le pase
     * el servicio llamada "servicio" como parametro a este metodo.
     * 
     * 
     * @Service(mi_servicio)
     */
    public function setServicio(Servicio $servicio)
    {
        $this->servicio = $servicio;
    }

    public function mensaje($msj)
    {
        $this->servicio->show("Imprimiendo desde ServicioDependiente: $msj");
    }

    public function showMethod()
    {
        $this->servicio->show(sprintf("Método de la Petición: <b>%s</b>",$this->request->getMethod()));
    }

}