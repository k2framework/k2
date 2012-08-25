<?php

namespace KumbiaPHP\Kernel;

use KumbiaPHP\Kernel\Request;
use KumbiaPHP\Kernel\Response;

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
