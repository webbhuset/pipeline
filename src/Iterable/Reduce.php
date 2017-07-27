<?php

namespace Webbhuset\Whaskell\Iterable;

use Webbhuset\Whaskell\AbstractFunction;
use Webbhuset\Whaskell\FunctionSignature;
use Webbhuset\Whaskell\WhaskellException;

class Reduce extends AbstractFunction
{
    protected $callback;
    protected $initialValue;
    protected $carry;

    public function __construct($callback, $initialValue)
    {
        $canBeUsed = FunctionSignature::canBeUsedWithArgCount($callback, 2, false);

        if ($canBeUsed !== true) {
            throw new WhaskellException($canBeUsed . ' Eg. function($carry, $item)');
        }

        $this->callback     = $callback;
        $this->initialValue = $initialValue;
        $this->carry        = $initialValue;
    }

    protected function invoke($items, $finalize = true)
    {
        $newItems = [];

        foreach ($items as $item) {
            $this->carry = call_user_func_array($this->callback, [$this->carry, $item]);
        }

        if ($finalize && $this->carry !== $this->initialValue) {
            yield $this->carry;
        }
    }
}
