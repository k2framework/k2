<?php

namespace KumbiaPHP\Kernel;

/**
 * Description of RouterInterface
 *
 * @author manuel
 */
interface RouterInterface
{

    public function redirect($url = NULL);

    public function toAction($action);

    public function getPublicPath();
}