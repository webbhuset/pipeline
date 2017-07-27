<?php

namespace Webbhuset\Whaskell\Flow;

use Webbhuset\Whaskell\AbstractFunction;
use Webbhuset\Whaskell\FunctionInterface;
use Webbhuset\Whaskell\Observe\ObserverInterface;
use Webbhuset\Whaskell\WhaskellException;

class Fork extends AbstractFunction
{
    protected $functions;

    public function __construct(array $functions)
    {
        foreach ($functions as $idx => $function) {
            if ($function === false) {
                unset($functions[$idx]);
                continue;
            }

            if (!$function instanceof FunctionInterface) {
                // TODO: toString on $function
                $class = is_object($function) ? get_class($function) : $function;
                throw new WhaskellException("Function {$idx} ({$class}) does not implement FunctionInterface.");
            }
        }

        $this->functions = $functions;
    }

    protected function invoke($items, $finalize = true)
    {
        foreach ($items as $item) {
            foreach ($this->functions as $function) {
                $results = $function([$item], false);
                foreach ($results as $res) {
                    yield $res;
                }
            }
        }

        if ($finalize) {
            foreach ($this->functions as $function) {
                $results = $function([], true);
                foreach ($results as $res) {
                    yield $res;
                }
            }
        }
    }

    public function registerObserver(ObserverInterface $observer)
    {
        foreach ($this->functions as $function) {
            $function->registerObserver($observer);
        }
    }
}
