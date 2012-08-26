<?php

namespace KumbiaPHP\Kernel;

use KumbiaPHP\Kernel\Request;
use KumbiaPHP\Di\Container\ContainerInterface;

/**
 *
 * @author manuel
 */
interface KernelInterface
{

    /**
     * @param Request $request
     * 
     * @return Response 
     */
    public function execute(Request $request);
    
    /**
     * @return ContainerInterface 
     */
    public function getContainer();

    /**
     * @return array 
     */
    public function getModules();

    /**
     * @return string 
     */
    public function getDefaultModule();

    public function getDefaultController();

    public function getDefaultAction();

    /**
     * @return Request 
     */
    public function getRequest();

    public function getAppPath();
}
