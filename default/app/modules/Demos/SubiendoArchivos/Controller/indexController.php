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

            if ($this->getRequest()->files->has('imagen')) {
                $file = Upload::factory($this->getRequest(), 'imagen');
                if ($file->save(uniqid())) {
                    App::get('flash')->success("El archivo se subió con exito...!!!");
                } else {
                    App::get('flash')->error($file->getErrors());
                }
            }
        }
    }

}