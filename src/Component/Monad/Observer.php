<?php

namespace Webbhuset\Bifrost\Core\Component\Monad;

use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Webbhuset\Bifrost\Core\BifrostException;

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
        foreach ($items as $key => $item) {
            if (is_string($key) && $key == 'event') {
                $name       = $item->data[1];
                $observers  = $this->getObservers($name);
                if ($observers) {
                    foreach ($observers as $observer) {
                        call_user_func_array($observer, $item->data);
                    }
                }
            }
            yield $key => $item;
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
