<?php

namespace KumbiaPHP\Kernel\Controller;

use KumbiaPHP\Kernel\Request;
use KumbiaPHP\Kernel\Response;

/**
 * Description of Controller
 *
 * @author manuel
 */
class Controller
{

    /**
     *
     * @var Request 
     */
    private $request;

    /**
     *
     * @var Response 
     */
    private $response;

    /**
     *
     * @var string 
     */
    private $methodName;

    /**
     *
     * @var string 
     */
    private $viewName;

    /**
     *
     * @var string 
     */
    private $viewFileName;

    public function __construct(Request $request, $methodName)
    {
        $this->request = $request;
        $this->response = new Response();
        $this->methodName = $methodName;
        $this->viewName = $methodName;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        $r = new \ReflectionObject($this);

        $moduleDir = dirname(dirname($r->getFileName()));
        $controllerName = preg_replace('/Controller$/', '', $r->getShortName());

        $viewFileName = $moduleDir . '/View/' . $controllerName . '/' . $this->viewName . '.phtml';

        if (!file_exists($viewFileName)) {
            throw new \LogicException(sprintf("No existe la Vista <b>%s</b> para el controlador <b>%s</b> en el Módulo <b>%s</b>", basename($viewFileName), $r->getShortName(), basename($moduleDir)));
        }

        $varsToView = array();
        foreach ($r->getProperties(\ReflectionProperty::IS_PUBLIC) as $var) {
            $varsToView[$var->getName()] = $var->getValue($this);
        }
        
        $this->viewFileName = $viewFileName;

        $this->response->setContent($this->getResponseString($varsToView));

        return $this->response;
    }

    private function getResponseString($vars)
    {
        ob_start();
        extract($vars, EXTR_OVERWRITE);
        
        unset($vars);

        require $this->viewFileName;
        
        return ob_get_clean();
    }

}