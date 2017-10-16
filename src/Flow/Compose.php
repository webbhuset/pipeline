<?php

namespace Webbhuset\Whaskell\Flow;

use Webbhuset\Whaskell\AbstractFunction;
use Webbhuset\Whaskell\Constructor as F;
use Webbhuset\Whaskell\FunctionInterface;
use Webbhuset\Whaskell\Observe\ObserverInterface;
use Webbhuset\Whaskell\WhaskellException;

class Compose extends AbstractFunction
{
    protected $functions;

    public function __construct(array $functions)
    {
        $treeToLeaves = F::TreeToLeaves();

        $functions = iterator_to_array($treeToLeaves([$functions]));

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

        $flattenedFunctions = [];
        foreach ($functions as $function) {
            if ($function instanceof self) {
                foreach ($function->getFunctions() as $childFunction) {
                    $flattenedFunctions[] = $childFunction;
                }
            } else {
                $flattenedFunctions[] = $function;
            }
        }

        $this->functions = $flattenedFunctions;
    }

    protected function invoke($items, $finalize = true)
    {
        foreach ($this->functions as $function) {
            $items = $function($items, $finalize);
        }

        return $items;
    }

    public function registerObserver(ObserverInterface $observer)
    {
        foreach ($this->functions as $function) {
            $function->registerObserver($observer);
        }
    }

    public function getFunctions()
    {
        return $this->functions;
    }
}
