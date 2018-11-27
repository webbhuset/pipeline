<?php

namespace Webbhuset\Whaskell\Iterable;

use Webbhuset\Whaskell\FunctionInterface;
use Webbhuset\Whaskell\FunctionSignature;
use Webbhuset\Whaskell\WhaskellException;

class Scan implements FunctionInterface
{
    protected $callback;
    protected $carry;
    protected $initialValue;


    public function __construct(callable $callback, $initialValue = [])
    {
        $canBeUsed = FunctionSignature::canBeUsedWithArgCount($callback, 2, false);

        if ($canBeUsed !== true) {
            throw new WhaskellException($canBeUsed . ' Eg. function($value, $carry)');
        }

        $this->callback     = $callback;
        $this->carry        = $initialValue;
        $this->initialValue = $initialValue;
    }

    public function __invoke($values, $finalize = true)
    {
        foreach ($values as $value) {
            yield $this->carry;

            $this->carry = call_user_func($this->callback, $value, $this->carry);
        }

        if ($finalize) {
            yield $this->carry;

            $this->carry = $this->initialValue;
        }
    }
}
