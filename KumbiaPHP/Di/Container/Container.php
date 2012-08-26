<?php

namespace KumbiaPHP\Di\Container;

use KumbiaPHP\Di\Container\ContainerInterface;
use KumbiaPHP\Di\DependencyInjectionInterface as Di;
use KumbiaPHP\Di\Definition\DefinitionManager;
use KumbiaPHP\Di\Container\Services;
use KumbiaPHP\Di\Definition\Service;

/**
 * Description of Container
 *
 * @author manuel
 */
class Container implements ContainerInterface
{

    /**
     * 
     * @var Services
     */
    protected $services;

    /**
     *
     * @var Di 
     */
    protected $di;

    /**
     *
     * @var DefinitionManager 
     */
    protected $definitioManager;

    public function __construct(Di $di, DefinitionManager $dm = NULL)
    {
        $this->services = new Services();
        $this->di = $di;
        $this->definitioManager = $dm ? : new DefinitionManager();
        
        //agregamos al container como servicio.
        $this->set('container', $this); 
    }

    public function get($id)
    {
        if ($this->services->has($id)) {
            //si existe el servicio lo devolvemos
            return $this->services->get($id);
        }
        //si no existe debemos crearlo
        //buscamos el servicio en el contenedor de servicios
        if (!$this->definitioManager->hasService($id)) {
            throw new \Exception(sprintf('No existe el servicio <b>%s</b>', $id));
        }

        $serviceClass = $this->definitioManager->getService($id)->getClassName();

        $this->set($id, $this->di->newInstance($serviceClass, $this));
        return $this->services->get($id);
    }

    public function has($id)
    {
        return $this->services->has($id);
    }

    public function set($id, $object)
    {
        $this->services->replace($id, $object);
        //y lo agregamos a las definiciones. (solo será a gregado si no existe)
        $this->definitioManager->addService(new Service('container', get_class($object)));
    }

}