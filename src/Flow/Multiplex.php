<?php

namespace Webbhuset\Whaskell\Flow;

use Webbhuset\Whaskell\Constructor as F;
use Webbhuset\Whaskell\FunctionInterface;
use Webbhuset\Whaskell\FunctionSignature;
use Webbhuset\Whaskell\WhaskellException;

class Multiplex implements FunctionInterface
{
    protected $callback;
    protected $functions;


    /**
     * Construct.
     *
     * @param callable $callback
     * @param array $functions
     *
     * @return void
     */
    public function __construct(callable $callback, array $functions)
    {
        $canBeUsed = FunctionSignature::canBeUsedWithArgCount($callback, 1);

        if ($canBeUsed !== true) {
            throw new WhaskellException($canBeUsed . ' e.g. function($value)');
        }

        foreach ($functions as $key => $function) {
            if ($function === false) {
                unset($functions[$key]);

                continue;
            }

            if (is_array($function)) {
                $function = F::Compose($function);
                $functions[$key] = $function;
            } elseif (!$function instanceof FunctionInterface) {
                $class = is_object($function) ? get_class($function) : $function;

                throw new WhaskellException("Function {$idx} ({$class}) does not implement FunctionInterface.");
            }
        }

        $this->callback     = $callback;
        $this->functions    = $functions;
    }

    public function __invoke($values, $keepState = false)
    {
        foreach ($values as $value) {
            $key = call_user_func($this->callback, $value);

            if (!isset($this->functions[$key])) {
                throw new WhaskellException("Unknown multiplex function {$key}.");
            }

            $results = call_user_func($this->functions[$key], [$value], true);

            foreach ($results as $result) {
                yield $result;
            }
        }

        if (!$keepState) {
            foreach ($this->functions as $function) {
                $results = $function([], false);

                foreach ($results as $result) {
                    yield $result;
                }
            }
        }
    }
}
