<?php

namespace KumbiaPHP\Kernel\Controller;

use KumbiaPHP\Di\Container\ContainerInterface;
use KumbiaPHP\Kernel\Request;

/**
 * Description of Controller
 *
 * @author manuel
 */
class Controller
{

    /**
     *
     * @var ContainerInterface; 
     */
    private $container;
    protected $view = 'index';
    protected $template = 'default';

    /**
     * @Service(container,$container)
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     *
     * @return object
     */
    protected function get($id)
    {
        return $this->container->get($id);
    }

    /**
     *
     * @return Request 
     */
    protected function getRequest()
    {
        return $this->container->get('request');
    }
    
    protected function setView($view, $template = FALSE)
    {
        $this->view = $view;
        if ($template !== FALSE)
        {
            $this->setTemplate($template);
        }
    }
    
    protected function setTemplate($template)
    {
        $this->template = $template;
    }

}