<?php

namespace Webbhuset\Pipeline\Flow;

use Webbhuset\Pipeline\Constructor as F;
use Webbhuset\Pipeline\FunctionInterface;
use Webbhuset\Pipeline\FunctionSignature;

class Factory implements FunctionInterface
{
    protected $callback;


    public function __construct(callable $callback)
    {
        $canBeUsed = FunctionSignature::canBeUsedWithArgCount($callback, 1, false);

        if ($canBeUsed !== true) {
            throw new \InvalidArgumentException($canBeUsed . ' e.g. function($value)');
        }

        $this->callback = $callback;
    }

    public function __invoke($values, $keepState = false)
    {
        foreach ($values as $value) {
            $function = call_user_func($this->callback, $value);

            if (is_array($function)) {
                $function = F::Compose($function);
            }

            if (!$function instanceof FunctionInterface) {
                throw new \InvalidArgumentException('Function must implement FunctionInterface.');
            }

            foreach ($function([$value], false) as $outValue) {
                yield $outValue;
            }
        }
    }
}
