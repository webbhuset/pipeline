<?php

namespace Webbhuset\Whaskell\Flow;

use Webbhuset\Whaskell\WhaskellException;
use Webbhuset\Whaskell\Dispatch\Data\DataInterface;
use Webbhuset\Whaskell\FunctionSignature;

class Multiplex
{
    protected $conditionCallback;
    protected $functions;

    /**
     * Construct.
     *
     * @param callable $conditionCallback
     * @param array $functions
     *
     * @return void
     */
    public function __construct(callable $conditionCallback, array $functions)
    {
        $canBeUsed = FunctionSignature::canBeUsedWithArgCount($conditionCallback, 1);

        if ($canBeUsed !== true) {
            throw new WhaskellException($canBeUsed . ' Eg. function($item)');
        }

        foreach ($functions as $key => $function) {
            if ($function === false) {
                unset($functions[$key]);
                continue;
            }

            if (!is_callable($function)) {
                // TODO: toString on $function
                $class = get_class($cfunction);
                throw new WhaskellException("Function {$idx} ({$class}) is not callable");
            }

            // TODO: Validate callable.
        }

        $this->conditionCallback    = $conditionCallback;
        $this->functions            = $functions;
    }

    public function __invoke($items, $finalize = true)
    {
        foreach ($items as $item) {
            if ($item instanceof DataInterface) {
                yield $item;
                continue;
            }

            $key = call_user_func($this->conditionCallback, $item);

            if (isset($this->functions[$key])) {
                $function   = $this->functions[$key];
                $results    = $function([$item], false);
                foreach ($results as $result) {
                    yield $result;
                }
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
