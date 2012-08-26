<?php

namespace KumbiaPHP\Di;

use KumbiaPHP\Di\Container\ContainerInterface;

/**
 * 
 * @author manuel
 */
interface DependencyInjectionInterface
{

    public function newInstance($class, ContainerInterface $container);
}
