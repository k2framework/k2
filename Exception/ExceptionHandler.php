<?php

namespace KumbiaPHP\Kernel\Exception;

use KumbiaPHP\Kernel\Response;

/**
 * Description of ExceptionHandler
 *
 * @author manuel
 */
class ExceptionHandler
{

    static public function handle(\Exception $e)
    {
        $HTML = sprintf('
<html>
    <head>
        <title>Error</title>
    </head>
    <body>
        <h1>%s</h1>
        <p>%s<p>
        <p>%s<p>
    </body>
</html>', basename(get_class($e)), $e->getMessage(), join('<br>',explode('#',$e->getTraceAsString())));
        
        $response = new Response($HTML,$e->getCode());
        $response->send();
    }

}