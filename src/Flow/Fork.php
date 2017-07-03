<?php

namespace Webbhuset\Whaskell\Flow;

use Webbhuset\Whaskell\WhaskellException;
use Webbhuset\Whaskell\Dispatch\Data\DataInterface;

class Fork
{
    protected $functions;

    public function __construct(array $functions)
    {
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
        foreach ($items as $item) {
            if ($item instanceof DataInterface) {
                yield $item;
                continue;
            }

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
}
