<?php

namespace KumbiaPHP\Di\Reader;

/**
 * Description of AnnotationReader
 *
 * @author manuel
 */
class AnnotationReader
{

    protected $services;
    protected $class;

    public function __construct($class)
    {
        $this->class = new \ReflectionClass($class);
        $this->services = $this->readClasses = array();
    }

    /**
     *
     * @return \ReflectionClass 
     */
    public function getClass()
    {
        return $this->class;
    }

    public function getServices()
    {

        $this->readComments($this->class);
        return $this->services;
    }

    protected function readComments(\ReflectionClass $class)
    {
        $pregConstruct = '/@Service\((?<service>.*?),(?<parameter>.*?)\)/';
        $pregMethod = '/@Service\((?<service>.*?)\)/';

        $constructName = $class->getConstructor() ? $class->getConstructor()->getName() : NULL;

        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {

            if ($constructName === $method->getName()) {
                if (preg_match_all($pregConstruct, $method->getDocComment(), $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $service) {
                        //voy a gregando los servicios encontrados para cada propiedad
                        $param = substr(trim($service['parameter']), 1); //le quitamos el $
                        $this->services[$method->getName()][$param] = trim($service['service']);
                    }
                }
            } else {
                if (preg_match_all($pregMethod, $method->getDocComment(), $matches)) {
                    //si no es el constructor debe aceptar un solo servicio
                    $this->services[$method->getName()] = trim($matches['service'][0]);
                }
            }
        }
    }

}