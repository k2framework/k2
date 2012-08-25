<?php

namespace KumbiaPHP\Kernel;

use KumbiaPHP\Kernel\RouterInterface;
use KumbiaPHP\Kernel\Request;

/**
 * Description of Router
 *
 * @author manuel
 */
class Router implements RouterInterface
{

    protected $controllerPath;
    protected $publicPath;

    public function __construct($publicPath)
    {
        $this->publicPath = $publicPath;
    }

    public function getPublicPath()
    {
        
    }

    public function redirect($url = NULL)
    {
        $url = $this->publicPath . ltrim($url, '/');
        header("Location: $url");
    }

    public function toAction($action)
    {
        $this->redirect("{$this->controllerPath}/{$action}");
    }

}