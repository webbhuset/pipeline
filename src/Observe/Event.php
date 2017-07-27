<?php

namespace Webbhuset\Whaskell\Observe;

use Webbhuset\Whaskell\WhaskellException;

class Event extends AbstractObserver
{
    protected $events;

    public function __construct($function, array $events)
    {
        parent::__construct($function);

        foreach ($events as $name => $observers) {
            foreach ($observers as $observer) {
                if (!is_callable($observer)) {
                    throw new WhaskellException("Observer on event '{$name}' is not callable");
                }
            }
        }

        $this->events = $events;
    }

    public function observeEvent($name, $item, $data, $contexts = [])
    {
        $functions = $this->getEventFunctions($name);
        if ($functions) {
            foreach ($functions as $function) {
                $function($item, $data, $contexts);
            }
        }

        if ($this->observer) {
            $this->observer->observeEvent($name, $item, $data, $contexts);
        }
    }

    public function observeError($item, $data, $contexts = [])
    {
        $this->observeEvent('error', $item, $data, $contexts);
    }

    protected function getEventFunctions($name)
    {
        if (isset($this->events[$name])) {
            return $this->events[$name];
        }

        return false;
    }
}
