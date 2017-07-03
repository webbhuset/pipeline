<?php

namespace Webbhuset\Whaskell\Flow;

use Webbhuset\Whaskell\WhaskellException;

class Compose
{
    protected $functions;

    public function __construct(array $functions)
    {
        $functions = $this->getArrayLeaves($functions);

        foreach ($functions as $idx => $function) {
            if ($function === false) {
                unset($functions[$idx]);
                continue;
            }

            if (!is_callable($function)) {
                // TODO: toString on $function
                $class = get_class($function);
                throw new WhaskellException("Function {$idx} ({$class}) is not callable");
            }

            // TODO: Validate callable.
        }

        $this->functions = $functions;
    }

    public function __invoke($items, $finalize = true)
    {
        foreach ($this->functions as $function) {
            $items = $function($items, $finalize);
        }

        return $items;
    }

    protected function getArrayLeaves($array)
    {
        return iterator_to_array(
            new RecursiveIteratorIterator(
                new RecursiveArrayIterator(
                    $array,
                    RecursiveArrayIterator::CHILD_ARRAYS_ONLY
                )
            ),
            false
        );
    }
}
