<?php

namespace KumbiaPHP\EventDispatcher;

use KumbiaPHP\EventDispatcher\Event;

/**
 * Description of EventDispatcherInterface
 *
 * @author manuel
 */
interface EventDispatcherInterface
{

    public function dispatch($eventName, Event $event);

    public function addListenerr($eventName, $listener);

    public function hasListenerr($eventName, $listener);

    public function removeListener($eventName, $listener);
}