<?php

namespace KumbiaPHP\Di;

use KumbiaPHP\Di\DependencyInjectionInterface;
use KumbiaPHP\Di\Container\Container;
use KumbiaPHP\Di\Reader\AnnotationReader;

/**
 * @Service(mi_servicio,$propiedad)
 * @Service( otro, $otraPropiedad)
 *
 * @author manuel
 */
class DependencyInjection implements DependencyInjectionInterface
{

    /**
     *  @var Container
     */
    protected $container;

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
    private $queue = array();
    private $isQueue = FALSE;

    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    public function newInstance($id, $className)
    {
        if (is_string($className)) {

            $reader = new AnnotationReader($className);
            $services = $reader->getServices();
            $class = $reader->getClass();
            //
        } elseif (!$className instanceof \ReflectionClass) {
            throw new \Exception("Tipo inesperado para el metodo newInstance del Inyector de Dependencias");
        }

        return $this->createInstance($id, $class);
    }

    protected function getArgumentsFromConstruct($id, \ReflectionClass $class, array $services = array())
    {
        $args = array();
        //lo agregamos a la cola hasta que se creen los servicios del
        //que depende
        if (!$this->isQueue) {
            $this->addToQueue($id);
        }
        if (isset($services['__construct']) && count($services['__construct'])) {
            foreach ($class->getConstructor()->getParameters() as $param) {
                if (isset($services['__construct'][$param->getName()])) {
                    $service = $services['__construct'][$param->getName()];
                    $args[$param->getName()] = $this->container->get($service);
                } else {
                    //por ahora NULL
                    $args[$param->getName()] = NULL;
                }
            }
        }
        //al tener los servicios que necesitamos
        //quitamos al servicio en construccion de la cola
        $this->removeToQueue($id);
        return $args;
    }

    /**
     *
     * @param type $class 
     */
    protected function setOtherServices($id, $object, array $services = array())
    {
        unset($services['__construct']);

        foreach ($services as $method => $service_id) {
            var_dump("inyectando el servicio $service_id en $id");
            $object->$method($this->container->get($service_id));
        }
    }

    protected function createInstance($id, \ReflectionClass $class)
    {
        $arguments = $this->getArgumentsFromConstruct($id, $class, $services);

        $instance = $class->newInstanceArgs($argsConstruct);

        //agregamos la instancia del objeto al contenedor.
        $this->container->set($id, $instance);

        $this->injectObjectIntoServicesQueue();

        $this->setOtherServices($id, $instance, $services);

        $this->isQueue = FALSE;

        return $instance;
    }

    protected function injectObjectIntoServicesQueue()
    {
        $this->isQueue = TRUE;
        foreach ($this->queue as $id => $class) {
            $this->newInstance($id, $class);
        }
        $this->isQueue = FALSE;
    }

    protected function inQueue($id)
    {
        return isset($this->queue[$id]);
    }

    protected function addToQueue($id, \ReflectionClass $class)
    {
        var_dump("a la cola $id", $this->queue);
        //si el servicio actual aparece en la cola de servicios
        //indica que dicho servicio tiene una dependencia a un servicio 
        //que depende de este, por lo que hay una dependencia circular.
        if ($this->inQueue($id)) {
            var_dump("problema con servicio $id", $this->queue);
            throw new \Exception("Se ha Detectado una Dependencia Circular entre Servicios $id");
        }
        $this->queue[$id] = $class;
    }

    protected function removeToQueue($id)
    {
        if ($this->inQueue($id)) {
            unset($this->queue[$id]);
            var_dump("fuera de la cola $id", $this->queue);
        }
    }

}