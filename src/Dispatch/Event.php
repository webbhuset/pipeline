<?php

namespace Webbhuset\Whaskell\Dispatch;

use Webbhuset\Whaskell\AbstractFunction;

class Event extends AbstractFunction
{
    protected $name;
    protected $bind;
    protected $useCallback = false;
    protected $callback;
    protected $observer;

    public function __construct($idOrCallable, $idOrBind = null, $bind = null)
    {
        $args   = func_get_args();
        $argOne = array_shift($args);

        if (is_callable($argOne)) {
            $this->callback     = $argOne;
            $this->useCallback  = true;
            $name               = array_shift($args);
            $bind               = array_shift($args);
        } else {
            $name               = $argOne;
            $bind               = array_shift($args);
        }

        $this->name = $name;
        $this->bind = $bind;
    }

    protected function invoke($items, $finalize = true)
    {
        if (!$this->observer) {
            foreach ($items as $item) {
                yield $item;
            }

            return;
        }

        foreach ($items as $item) {
            if (!$this->useCallback) {
                $this->observer->observeEvent($this->name, $item, $this->bind);
            } else {
                $result = call_user_func($this->callback, $item);
                if (is_bool($result)) {
                    $result = $item;
                }
                $this->observer->observeEvent($this->name, $result, $this->bind);
            }

            yield $item;
        }
    }
}

