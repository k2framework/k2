<?php

namespace Index\Controller;

use KumbiaPHP\Kernel\Controller\Controller;

/**
 * Description of IndexController
 *
 * @author manuel
 */
class PagesController extends Controller
{

    protected $limitParams = FALSE;

    public function show()
    {
        $this->setView(implode('/', $this->parameters));
    }

}