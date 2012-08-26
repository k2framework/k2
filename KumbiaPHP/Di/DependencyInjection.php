<?php

namespace KumbiaPHP\Di;

use KumbiaPHP\Di\DependencyInjectionInterface;
use KumbiaPHP\Di\Container\ContainerInterface;
use KumbiaPHP\Di\Reader\AnnotationReader;

/**
 * @Service(mi_servicio,$propiedad)
 * @Service( otro, $otraPropiedad)
 *
 * @author manuel
 */
class DependencyInjection implements DependencyInjectionInterface
{

    public function newInstance($class, ContainerInterface $container)
    {
        return $this->getServicesToInject($class, $container);
    }

    /**
     *
     * @param type $class 
     */
    public function getServicesToInject($class, ContainerInterface $container)
    {
        $reader = new AnnotationReader($class);
        $services = $reader->getServices();
                
        $class = $reader->getClass();

        $args = array();
        if (isset($services['__construct']) && count($services['__construct'])) {
            foreach ($class->getConstructor()->getParameters() as $param) {
                if (isset($services['__construct'][$param->getName()])) {
                    $service = $services['__construct'][$param->getName()];
                    $args[$param->getName()] = $container->get($service);
                }else{
                    $args[$param->getName()] = NULL;                    
                }
            }
        }
        
        unset($services['__construct']);

        $object = $class->newInstanceArgs($args);
        
        foreach ($services as $method => $service) {
            //por ahora los metodos aceptaran un solo servicio
            $object->$method($container->get($service));
        }


        return $object;
    }

}