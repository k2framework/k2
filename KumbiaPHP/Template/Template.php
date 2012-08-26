<?php

namespace KumbiaPHP\Template;

use KumbiaPHP\Kernel\Response;

/**
 * Description of Template
 *
 * @author manuel
 */
class Template
{

    protected $template;
    protected $view;
    protected $variables;
    protected $content;

    public function render($template, $view, array $params = array())
    {
        $this->template = $template;
        $this->view = $view;
        $this->variables = $params;

        return $this->getContent();
    }

    protected function getContent()
    {
        extract($this->variables, EXTR_OVERWRITE);
        //si va a mostrar vista
        if ($this->view !== NULL) {

            if (!file_exists($this->view)) {
                throw new \LogicException(sprintf("No existe la Vista <b>%s</b>", basename($this->view)));
            }
            ob_start();
            require_once $this->view;
            $this->content = ob_get_clean();
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
        
        return new Response($this->content);
    }

}