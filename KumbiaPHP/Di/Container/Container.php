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

    /**
     * Contiene los servicios que se han ido solicitando a partir de un servicio
     * inicial que depende de otros servicios no creados aun.
     * 
     * Por cada solicitud de un servicio no creado, se debe verificar que ese
     * servicio no esté ya en la cola, porque estariamos en presencia de 
     * una dependencia circular entre servicios, donde un servicio A depende
     * de un servicio B que depende del servicio A.
     * 
     * @var array 
     */
    private $queue;

    public function __construct(Di $di, DefinitionManager $dm = NULL)
    {
        $this->services = new Services();
        $this->di = $di;
        $this->definitioManager = $dm ? : new DefinitionManager();

        $this->queue = array();

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

        //si el servicio actual aparece en la cola de servicios
        //indica que dicho servicio tiene una dependencia a un servicio 
        //que depende de este, por lo que hay una dependencia circular.
        if (in_array($id, $this->queue)) {
            throw new \Exception('Se ha Detectado una Dependencia Circular entre Servicios');
        }

        array_push($this->queue, $id); //agregamos el $id del servicio a la cola de servicios

        $serviceClass = $this->definitioManager->getService($id)->getClassName();

        $this->set($id, $this->di->newInstance($serviceClass, $this));
        
        //antes de devolver la instancia creada, quitamos el id
        //de la cola de servicios.
        array_shift($this->queue); 
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