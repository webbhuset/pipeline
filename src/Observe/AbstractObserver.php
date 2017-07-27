<?php

namespace Webbhuset\Whaskell\Observe;

use Webbhuset\Whaskell\AbstractFunction;
use Webbhuset\Whaskell\Constructor as F;
use Webbhuset\Whaskell\FunctionInterface;
use Webbhuset\Whaskell\WhaskellException;

abstract class AbstractObserver extends AbstractFunction implements ObserverInterface
{
    protected $observer;
    protected $function;

    public function __construct($function)
    {
        if ($function === null) {
            return;
        }

        if (is_array($function)) {
            $function = F::Compose($function);
        }

        if (!$function instanceof FunctionInterface) {
            throw new WhaskellException('Function must implement FunctionInterface');
        }

        $function->registerObserver($this);

        $this->function = $function;
    }

    public abstract function reconstruct($function);

    protected function invoke($items, $finalize = true)
    {
        if (!$this->function) {
            throw new WhaskellException('Observer function cannot be null.');
        }

        return call_user_func($this->function, $items, $finalize);
    }

    public function observeEvent($name, $item, $data, $contexts = [])
    {
        if ($this->observer) {
            $this->observer->observeEvent($name, $item, $data, $contexts);
        }
    }

    public function observeSideEffect($name, $item, $data)
    {
        if ($this->observer) {
            $item = $this->observer->observeSideEffect($name, $item, $data);
        }

        return $item;
    }

    public function observeError($item, $data, $contexts = [])
    {
        if ($this->observer) {
            $this->observer->observeError($item, $data, $contexts);
        }
    }
}
