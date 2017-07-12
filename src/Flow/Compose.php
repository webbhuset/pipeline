<?php

namespace Webbhuset\Whaskell\Flow;

use Webbhuset\Whaskell\Constructor as F;
use Webbhuset\Whaskell\WhaskellException;

class Compose
{
    protected $functions;

    public function __construct(array $functions)
    {
        $treeToLeaves = F::TreeToLeaves();

        $functions = iterator_to_array($treeToLeaves($functions));

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
}
