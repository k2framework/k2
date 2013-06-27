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

    protected $limitParams = false;

    public function show_action()
    {
        $module = App::getContext('module');
        $view = '@' . $module['name'] . '/pages/' . join('/', $this->parameters);

        $this->setView($view);
    }

    public function status_action()
    {
        $this->config = App::getParameter('config');
        $this->appName = basename(dirname(APP_PATH));
        $this->defaultTimeZone = date_default_timezone_get();
    }

}