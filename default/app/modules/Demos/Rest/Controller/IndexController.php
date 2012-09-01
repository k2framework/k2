<?php

namespace Demos\Rest\Controller;

//use KumbiaPHP\Kernel\Controller\Controller;
use KumbiaPHP\Kernel\Request;
use KumbiaPHP\Kernel\Response;

/**
 * Ejemplo de un controlador REST FULL
 * 
 * Este controlador puede manejar peticiones de tipo rest, 
 * no hace falta que extienda de Controller si no va a realizar muchas tareas.
 *
 * @author maguirre
 */
class IndexController //extends Controller
{

    /**
     * Este filtro se ejecuta antes de la llamada a cualquier metodo del controlador
     * 
     * En este ejemplo espera que le pasen el Request como argumento, pero
     * esto es opcional.
     * 
     * @param Request $request
     * @return null|string
     * 
     * este filtro puede ó no retornar nada, ó retornar una cadena con el nombre
     * de la nueva acción a ejecutar en el controlador. 
     */
    protected function beforeFilter(Request $request)
    {
        //aqui le decimos que ejecute la accion que tenga el nombre
        //del metodo de petición.
        return strtolower($request->getMethod());
    }

    /**
     * Este método es llamado en las peticiones de tipo GET
     * ya que el filtro reescribe la acción a llamar dependiendo del metodo de la peticion
     * 
     * @return \KumbiaPHP\Kernel\Response 
     */
    public function get()
    {
        //creamos un arreglo de ejemplo para imprimirlo como json
        $data = array(
            'variable' => "Hola Mundo REST",
            'metodo' => "GET",
        );
        /*
         * retornamos un objeto RESPONSE donde su contenido es un json del areglo
         * el status es 200 y el content type será application/json
         */
        return new Response(json_encode($data), 200, array('Content-Type' => 'application/json'));
    }

    /**
     * Aplica los que para el metodo get
     * 
     * @return \KumbiaPHP\Kernel\Response 
     */
    public function post()
    {
        $data = array(
            'variable' => "Hola Mundo REST",
            'metodo' => "POST",
        );
        return new Response(json_encode($data), 200, array('Content-Type' => 'application/json'));
    }

    /**
     * Aplica los que para el metodo get
     * 
     * @return \KumbiaPHP\Kernel\Response 
     */
    public function put()
    {
        $data = array(
            'variable' => "Hola Mundo REST",
            'metodo' => "PUT",
        );
        return new Response(json_encode($data), 200, array('Content-Type' => 'application/json'));
    }

    /**
     * Aplica los que para el metodo get
     * 
     * @return \KumbiaPHP\Kernel\Response 
     */
    public function delete()
    {
        $data = array(
            'variable' => "Hola Mundo REST",
            'metodo' => "DELETE",
        );
        return new Response(json_encode($data), 200, array('Content-Type' => 'application/json'));
    }

}
