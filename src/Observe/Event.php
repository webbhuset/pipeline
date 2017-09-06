<?php

namespace Webbhuset\Whaskell\Observe;

use Webbhuset\Whaskell\WhaskellException;

class Event extends AbstractObserver
{
    protected $eventFunctions;

    public function __construct($function, $eventFunctions)
    {
        parent::__construct($function);

        if (is_array($eventFunctions)) {
            foreach ($eventFunctions as $name => $eventFunction) {
                if (!is_callable($eventFunction)) {
                    throw new WhaskellException("Observer method '{$name}' in array is not callable");
                }
            }
        }

        $this->eventFunctions = $eventFunctions;
    }

    public function observeEvent($name, $item, $data, $contexts = [])
    {
        $function = $this->getEventFunction($name);
        if ($function) {
            $function($item, $data, $contexts);
        }

        if ($this->observer) {
            $this->observer->observeEvent($name, $item, $data, $contexts);
        }
    }

    public function observeError($item, $data, $contexts = [])
    {
        $this->observeEvent('error', $item, $data, $contexts);
    }

    protected function getEventFunction($name)
    {
        if (is_array($this->eventFunctions) && isset($this->eventFunctions[$name])) {
            return $this->eventFunctions[$name];
        }

        if (is_object($this->eventFunctions)) {
            $method = [$this->eventFunctions, $name];
            if (is_callable($method)) {
                return $method;
            }
        }

        return false;
    }
}
