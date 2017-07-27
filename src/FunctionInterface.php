<?php

namespace Webbhuset\Whaskell;

use Webbhuset\Whaskell\Observe\ObserverInterface;

interface FunctionInterface
{
    public function __invoke($items, $finalize = true);
    public function registerObserver(ObserverInterface $observer);
}
