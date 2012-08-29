<?php

namespace KumbiaPHP\Kernel;

use KumbiaPHP\Kernel\Request;

/**
 * Description of RouteContext
 *
 * @author maguirre
 */
class AppContext
{

    protected $baseUrl;
    protected $appPath;
    protected $moduleDir;
    protected $namespaces;
    protected $currentUrl;
    protected $modules;
    protected $currentModule;

    public function __construct(Request $request, $appPath, $namespaces)
    {
        $this->baseUrl = $request->getBaseUrl();
        $this->appPath = $appPath;
        $this->currentUrl = $request->get('_url');
        $this->moduleDir = $appPath . '/modules/';
        $this->namespaces = $namespaces;
        $this->modules = $namespaces;
        //debemos excluir el namespace del dir del core del propio fw
        unset($this->modules['KumbiaPHP']);
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function getAppPath()
    {
        return $this->appPath;
    }

    public function getCurrentUrl()
    {
        return $this->currentUrl;
    }

    public function getModuleDir()
    {
        return $this->moduleDir;
    }

    public function getNamespaces()
    {
        return $this->namespaces;
    }

    public function getModules()
    {
        return $this->modules;
    }

}

