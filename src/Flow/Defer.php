<?php

namespace Webbhuset\Pipeline\Flow;

use Webbhuset\Pipeline\Constructor as F;
use Webbhuset\Pipeline\FunctionInterface;
use Webbhuset\Pipeline\FunctionSignature;

class Defer implements FunctionInterface
{
    protected $callback;
    protected $function;


    public function __construct(callable $callback)
    {
        $canBeUsed = FunctionSignature::canBeUsedWithArgCount($callback, 0, false);

        if ($canBeUsed !== true) {
            throw new \InvalidArgumentException($canBeUsed . ' e.g. function ()');
        }

        $this->callback = $callback;
    }

    public function __invoke($values, $keepState = false)
    {
        if (!$this->function) {
            $function = call_user_func($this->callback);

            if (is_array($function)) {
                $function = F::Compose($function);
            } elseif (!$function instanceof FunctionInterface) {
                throw new \InvalidArgumentException('Function must implement FunctionInterface.');
            }

            $this->function = $function;
        } else {
            $function = $this->function;
        }

        if (!$keepState) {
            $this->function = null;
        }

        return $function($values, $keepState);
    }
}
