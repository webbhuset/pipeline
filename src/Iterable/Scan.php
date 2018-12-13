<?php

namespace Webbhuset\Pipeline\Iterable;

use Webbhuset\Pipeline\FunctionInterface;
use Webbhuset\Pipeline\FunctionSignature;

class Scan implements FunctionInterface
{
    protected $callback;
    protected $carry;
    protected $initialValue;


    public function __construct(callable $callback, $initialValue = [])
    {
        $canBeUsed = FunctionSignature::canBeUsedWithArgCount($callback, 2, false);

        if ($canBeUsed !== true) {
            throw new \InvalidArgumentException($canBeUsed . ' Eg. function($value, $carry)');
        }

        $this->callback     = $callback;
        $this->carry        = $initialValue;
        $this->initialValue = $initialValue;
    }

    public function __invoke($values, $keepState = false)
    {
        foreach ($values as $value) {
            yield $this->carry;

            $this->carry = call_user_func($this->callback, $value, $this->carry);
        }

        if (!$keepState) {
            yield $this->carry;

            $this->carry = $this->initialValue;
        }
    }
}
