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
            throw new WhaskellException($canBeUsed . ' e.g. function($item)');
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

            // TODO: Validate callable.
        }

        $this->callback     = $callback;
        $this->functions    = $functions;
    }

    public function __invoke($items, $finalize = true)
    {
        foreach ($items as $item) {
            $key = call_user_func($this->callback, $item);

            if (isset($this->functions[$key])) {
                $function   = $this->functions[$key];
                $results    = $function([$item], false);
                foreach ($results as $result) {
                    yield $result;
                }
            } elseif ($key === false) {
                yield $item;
            } else {
                throw new WhaskellException("Unknown multiplex function {$key}.");
            }
        }

        if ($finalize) {
            foreach ($this->functions as $function) {
                $results = $function([], true);
                foreach ($results as $result) {
                    yield $result;
                }
            }
        }
    }
}
