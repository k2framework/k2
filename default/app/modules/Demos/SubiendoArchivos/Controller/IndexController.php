<?php

namespace Demos\SubiendoArchivos\Controller;

use KumbiaPHP\Kernel\Controller\Controller;
use KumbiaPHP\Kernel\Response;
use KumbiaPHP\Upload\Upload;

/**
 * Description of IndexController
 *
 * @author manuel
 */
class IndexController extends Controller
{

    public function index()
    {
        if ($this->getRequest()->isMethod('POST')) {

            if ($this->getRequest()->files->has('imagen')) {
                $file = Upload::factory($this->getRequest(), 'imagen');
            }
            var_dump($file->save(uniqid()));
            var_dump($file->getErrors());
            return new Response();
//            return new Response(json_encode($file->save()), 200, array(
//                        'Content-Type' => 'application/json'
//                    ));
        }
    }

}