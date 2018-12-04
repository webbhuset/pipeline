<?php

namespace Webbhuset\Pipeline\Flow;

use Webbhuset\Pipeline\Constructor as F;
use Webbhuset\Pipeline\FunctionInterface;
use Webbhuset\Pipeline\FunctionSignature;
use Webbhuset\Pipeline\PipelineException;

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
            throw new PipelineException($canBeUsed . ' e.g. function($value)');
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

                throw new PipelineException("Function {$idx} ({$class}) does not implement FunctionInterface.");
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
                throw new PipelineException("Unknown multiplex function {$key}.");
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
