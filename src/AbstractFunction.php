<?php

namespace Webbhuset\Whaskell;

use Webbhuset\Whaskell\Observe\ObserverInterface;

abstract class AbstractFunction implements FunctionInterface
{
    protected $observer;

    public function __invoke($items, $finalize = true)
    {
        return $this->invoke($items, $finalize);
    }

    protected abstract function invoke($items, $finalize = true);

    public function registerObserver(ObserverInterface $observer)
    {
        $this->observer = $observer;
    }
}
