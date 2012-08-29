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
    protected $parameters;
    protected $class;

    public function __construct($class)
    {
        $this->class = new \ReflectionClass($class);
        $this->parameters = $this->services = $this->readClasses = array();
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

        $this->readCommentsOfServices($this->class);
        return $this->services;
    }

    public function getParameters()
    {

        $this->readCommentsOfParameters($this->class);
        return $this->parameters;
    }

    protected function readCommentsOfServices(\ReflectionClass $class)
    {
        $pregConstruct = '/@Service\((?<service>.*?),(?<argument>.*?)\)/';
        $pregMethod = '/@Service\((?<service>.*?)\)/';

        $constructName = $class->getConstructor() ? $class->getConstructor()->getName() : NULL;

        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {

            if ($constructName === $method->getName()) {
                if (preg_match_all($pregConstruct, $method->getDocComment(), $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $service) {
                        //voy a gregando los servicios encontrados para cada propiedad
                        $param = substr(trim($service['argument']), 1); //le quitamos el $
                        $this->services['__construct'][$param] = trim($service['service']);
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

    protected function readCommentsOfParameters(\ReflectionClass $class)
    {
        $pregConstruct = '/@Parameter\((?<parameter>.*?),(?<argument>.*?)\)/';
        $pregMethod = '/@Parameter\((?<parameter>.*?)\)/';

        $constructName = $class->getConstructor() ? $class->getConstructor()->getName() : NULL;

        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {

            if ($constructName === $method->getName()) {
                if (preg_match_all($pregConstruct, $method->getDocComment(), $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $parameter) {
                        //voy a gregando los servicios encontrados para cada propiedad
                        $param = substr(trim($parameter['argument']), 1); //le quitamos el $
                        $this->parameters['__construct'][$param] = trim($parameter['parameter']);
                    }
                }
            } else {
                if (preg_match_all($pregMethod, $method->getDocComment(), $matches)) {
                    //si no es el constructor debe aceptar un solo servicio
                    $this->parameters[$method->getName()] = trim($matches['parameter'][0]);
                }
            }
        }
    }

}