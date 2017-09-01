<?php

namespace Webbhuset\Whaskell\Flow;

use Webbhuset\Whaskell\AbstractFunction;
use Webbhuset\Whaskell\Constructor as F;
use Webbhuset\Whaskell\FunctionInterface;
use Webbhuset\Whaskell\FunctionSignature;
use Webbhuset\Whaskell\Observe\ObserverInterface;
use Webbhuset\Whaskell\WhaskellException;

class Multiplex extends AbstractFunction
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

            if (is_array($function)) {
                $function = F::Compose($function);
                $functions[$key] = $function;
            } elseif (!$function instanceof FunctionInterface) {
                // TODO: toString on $function
                $class = get_class($function);
                throw new WhaskellException("Function {$idx} ({$class}) does not implement FunctionInterface.");
            }

            // TODO: Validate callable.
        }

        $this->conditionCallback    = $conditionCallback;
        $this->functions            = $functions;
    }

    protected function invoke($items, $finalize = true)
    {
        foreach ($items as $item) {
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

    public function registerObserver(ObserverInterface $observer)
    {
        foreach ($this->functions as $function) {
            $function->registerObserver($observer);
        }
    }
}
