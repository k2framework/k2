<?php

namespace Index\Controller;

use K2\Kernel\App;
use K2\Kernel\Controller\Controller;

/**
 * Description of IndexController
 *
 * @author manuel
 */
class pagesController extends Controller
{

    protected $limitParams = FALSE;

    public function show_action()
    {
        $module = App::getContext('module');
        $view = '@' . $module['name'] . '/pages/' . join('/', $this->parameters);
        
        $this->setView($view);
    }

}