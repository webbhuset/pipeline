<?php

namespace Webbhuset\Whaskell\Iterable;

use Webbhuset\Whaskell\FunctionInterface;
use Webbhuset\Whaskell\FunctionSignature;
use Webbhuset\Whaskell\WhaskellException;

class Reduce implements FunctionInterface
{
    protected $callback;
    protected $carry;


    public function __construct($callback, $initialValue = [])
    {
        $canBeUsed = FunctionSignature::canBeUsedWithArgCount($callback, 2, false);

        if ($canBeUsed !== true) {
            throw new WhaskellException($canBeUsed . ' Eg. function($item, $carry)');
        }

        $this->callback     = $callback;
        $this->carry        = $initialValue;
    }

    public function __invoke($items, $finalize = true)
    {
        foreach ($items as $item) {
            $this->carry = call_user_func($this->callback, $item, $this->carry);
        }

        if ($finalize && $this->carry) {
            yield $this->carry;
        }
    }
}
