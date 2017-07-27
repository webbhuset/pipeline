<?php

namespace Webbhuset\Whaskell\Observe;

class AppendContext extends AbstractObserver
{
    protected $context;

    public function __construct($function, $context)
    {
        parent::__construct($function);

        $this->context = $context;
    }

    public function observeEvent($name, $item, $data, $contexts = [])
    {
        if ($this->observer) {
            $contexts[] = $this->context;
            $this->observer->observeEvent($name, $item, $data, $contexts);
        }
    }

    public function observeError($item, $data, $contexts = [])
    {
        if ($this->observer) {
            $contexts[] = $this->context;
            $this->observer->observeError($item, $data, $contexts);
        }
    }
}
