<?php

namespace Webbhuset\Whaskell\Flow;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Webbhuset\Whaskell\AbstractFunction;
use Webbhuset\Whaskell\FunctionInterface;
use Webbhuset\Whaskell\Observe\ObserverInterface;
use Webbhuset\Whaskell\WhaskellException;

class Compose implements FunctionInterface
{
    protected $functions;


    public function __construct(array $functions)
    {
        $it = new RecursiveIteratorIterator(
            new RecursiveArrayIterator(
                $functions,
                RecursiveArrayIterator::CHILD_ARRAYS_ONLY
            )
        );
        $functions = iterator_to_array($it);

        foreach ($functions as $idx => $function) {
            if ($function === false) {
                unset($functions[$idx]);

                continue;
            }

            if (!$function instanceof FunctionInterface) {
                $class = is_object($function) ? get_class($function) : $function;

                throw new WhaskellException("Function {$idx} ({$class}) does not implement FunctionInterface.");
            }
        }

        $flattenedFunctions = array_reduce($functions, function($functions, $function) {
            if ($function instanceof self) {
                $functions = array_merge($functions, $function->getFunctions());
            } else {
                $functions[] = $function;
            }

            return $functions;
        }, []);

        $this->functions = $flattenedFunctions;
    }

    public function __invoke($values, $keepState = false)
    {
        foreach ($this->functions as $function) {
            $values = $function($values, $keepState);
        }

        return $values;
    }

    protected function getFunctions()
    {
        return $this->functions;
    }
}
