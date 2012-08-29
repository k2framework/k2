<?php

namespace KumbiaPHP\View;

use KumbiaPHP\Kernel\Response;
use KumbiaPHP\View\ViewContainer;
use KumbiaPHP\Di\Container\ContainerInterface;

/**
 * Description of Template
 *
 * @author manuel
 */
class View
{

    protected $template;
    protected $view;
    protected $variables;
    protected $content;

    /**
     * 
     * @var ContainerInterface 
     */
    private $container;

    /**
     * @Service(container,$container)
     * @param ContainerInterface $container 
     */
    public function __construct(ContainerInterface $container)
    {
        $this->variables['view'] = new ViewContainer($container);
    }

    public function render($template, $view, array $params = array(), Response $response = NULL)
    {
        $this->template = $template;
        $this->view = $view;
        $this->variables = array_merge($params, $this->variables);

        return $this->getContent($response);
    }

    protected function getContent(Response $response = NULL)
    {
        extract($this->variables, EXTR_OVERWRITE);
        //si va a mostrar vista
        if ($this->view !== NULL) {

            if (!file_exists($this->view)) {
                throw new \LogicException(sprintf("No existe la Vista <b>%s</b>", basename($this->view)));
            }
            ob_start();
            require_once $this->view;
            $this->variables['view']->content = $this->content = ob_get_clean();
        }
        if ($this->template !== NULL) {

            if (!file_exists($this->template)) {
                throw new \LogicException(sprintf("No existe El Template <b>%s</b>", basename($this->template)));
            }
            ob_start();
            $content = $this->content;
            require_once $this->template;
            $this->content = ob_get_clean();
        }

        if ($response instanceof Response) {
            $response->setContent($this->content);
            return $response;
        }

        return new Response($this->content);
    }

}