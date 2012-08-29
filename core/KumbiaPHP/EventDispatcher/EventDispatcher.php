<?php

namespace KumbiaPHP\EventDispatcher;

use KumbiaPHP\EventDispatcher\EventDispatcherInterface;
use KumbiaPHP\Di\Container\ContainerInterface;

/**
 * Description of EventDispatcher
 *
 * @author manuel
 */
class EventDispatcher implements EventDispatcherInterface
{

    protected $listeners = array();

    /**
     *
     * @var ContainerInterface 
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function dispatch($eventName, Event $event)
    {
        if (!array_key_exists($eventName, $this->listeners)) {
            return;
        }
        if (is_array($this->listeners[$eventName]) && count($this->listeners[$eventName])) {
            foreach ($this->listeners[$eventName] as $litener) {
                call_user_func($litener, $event);
                if ($event->isPropagationStopped()) {
                    break;
                }
            }
        }
    }

    //put your code here
    public function addListenerr($eventName, $listener)
    {
        if (!$this->hasListenerr($eventName, $listener)) {
            $this->listeners[$eventName][] = $listener;
        }
    }

    public function hasListenerr($eventName, $listener)
    {
        if (isset($this->listeners[$eventName])) {
            return in_array($listener, $this->listeners[$eventName]);
        } else {
            return FALSE;
        }
    }

    public function removeListener($eventName, $listener)
    {
        if ($this->hasListenerr($eventName, $listener)) {
            do {
                if ($listener === current($this->listeners[$eventName])) {
                    $key = key(current($this->listeners[$eventName]));
                    break;
                }
            } while (next($this->listeners[$eventName]));
        }
        unset($this->listeners[$eventName][$key]);
    }

}