<?php

namespace Webbhuset\Pipeline\Value;

use Webbhuset\Pipeline\FunctionInterface;
use Webbhuset\Pipeline\FunctionSignature;

class Observe implements FunctionInterface
{
    protected $callback;


    public function __construct(callable $callback)
    {
        $canBeUsed = FunctionSignature::canBeUsedWithArgCount($callback, 1);

        if ($canBeUsed !== true) {
            throw new \InvalidArgumentException($canBeUsed . ' e.g. function($value)');
        }

        $this->callback = $callback;
    }

    public function __invoke($values, $keepState = false)
    {
        foreach ($values as $value) {
            call_user_func($this->callback, $value);

            yield $value;
        }
    }
}
