<?php

namespace KumbiaPHP\EventDispatcher;

use KumbiaPHP\EventDispatcher\EventDispatcherInterface;

/**
 * Description of EventDispatcher
 *
 * @author manuel
 */
class EventDispatcher implements EventDispatcherInterface
{

    protected $listeners = array();

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
        return in_array($listener, $this->listeners[$eventName]);
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