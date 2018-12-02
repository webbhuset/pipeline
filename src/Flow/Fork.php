<?php

namespace Webbhuset\Whaskell\Flow;

use Webbhuset\Whaskell\Constructor as F;
use Webbhuset\Whaskell\FunctionInterface;
use Webbhuset\Whaskell\WhaskellException;

class Fork implements FunctionInterface
{
    protected $functions;


    public function __construct(array $functions)
    {
        foreach ($functions as $idx => $function) {
            if ($function === false) {
                unset($functions[$idx]);

                continue;
            }

            if (is_array($function)) {
                $function = F::Compose($function);
            } elseif (!$function instanceof FunctionInterface) {
                $class = is_object($function) ? get_class($function) : $function;

                throw new WhaskellException("Function {$idx} ({$class}) does not implement FunctionInterface.");
            }
        }

        $this->functions = $functions;
    }

    public function __invoke($values, $keepState = false)
    {
        foreach ($values as $value) {
            foreach ($this->functions as $function) {
                $results = $function([$value], true);

                foreach ($results as $res) {
                    yield $res;
                }
            }
        }

        if (!$keepState) {
            foreach ($this->functions as $function) {
                $results = $function([], false);

                foreach ($results as $res) {
                    yield $res;
                }
            }
        }
    }
}
