<?php

namespace KumbiaPHP\View\Helper;

use KumbiaPHP\View\Helper\AbstractHelper;

/**
 * Description of Html
 *
 * @author manuel
 */
class Html extends AbstractHelper
{

    public function img($src, $alt = NULL)
    {
        $src = $this->app->getBaseUrl() . $src;
        return "<img src=\"$src\" alt=\"$alt\" />";
    }

}