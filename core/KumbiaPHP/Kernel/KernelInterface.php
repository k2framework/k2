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
}
