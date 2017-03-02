<?php

namespace Webbhuset\Bifrost\Core\Component\Monad;

use Webbhuset\Bifrost\Core\BifrostException;
use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Webbhuset\Bifrost\Core\Data\ActionData\EventData;

class Observer implements ComponentInterface
{
    protected $events;

    public function __construct(array $events)
    {
        foreach ($events as $name => $observers) {
            foreach ($observers as $observer) {
                if (!is_callable($observer)) {
                    throw new BifrostException("Observer on event '{$name}' is not callable");
                }
            }
        }

        $this->events = $events;
    }

    public function process($items, $finalize = true)
    {
        foreach ($items as $item) {
            if ($item instanceof EventData) {
                $name       = $item->getName();
                $observers  = $this->getObservers($name);
                if ($observers) {
                    foreach ($observers as $observer) {
                        call_user_func($observer, $item->getItem(), $item->getData(), $item->getContexts());
                    }
                }
            }
            yield $item;
        }
    }

    protected function getObservers($name)
    {
        if (isset($this->events[$name])) {
            return $this->events[$name];
        }

        return false;
    }
}
