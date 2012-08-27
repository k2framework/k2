<?php

namespace KumbiaPHP\Kernel;

/**
 * Description of RouteContext
 *
 * @author maguirre
 */
class AppContext
{

    protected $baseUrl;
    protected $appPath;
    protected $modulesDir;
    protected $currentModule;
    protected $currentUrl;

    public function __construct()
    {
        
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function getAppPath()
    {
        return $this->appPath;
    }

    public function setAppPath($appPath)
    {
        $this->appPath = $appPath;
    }

    public function getModulesDir()
    {
        return $this->modulesDir;
    }

    public function setModulesDir($modulesDir)
    {
        $this->modulesDir = $modulesDir;
    }

    public function getCurrentModule()
    {
        return $this->currentModule;
    }

    public function setCurrentModule($currentModule)
    {
        $this->currentModule = $currentModule;
    }

    public function getCurrentUrl()
    {
        return $this->currentUrl;
    }

    public function setCurrentUrl($currentUrl)
    {
        $this->currentUrl = $currentUrl;
    }

}

