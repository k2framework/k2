<?php

namespace Demos\SubiendoArchivos\Controller;

use K2\Kernel\Controller\Controller;
use K2\Upload\Upload;
use K2\Kernel\App;

/**
 * Description of IndexController
 *
 * @author manuel
 */
class indexController extends Controller
{

    public function index_action()
    {
        if ($this->getRequest()->isMethod('POST')) {

            $file = Upload::factory('imagen');
            if ($file->saveRandom()) {
                App::get('flash')->success("El archivo se subió con exito...!!!");
            } else {
                App::get('flash')->error($file->getErrors());
            }
        }
    }

}