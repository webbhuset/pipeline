<?php

namespace Webbhuset\Pipeline\Value;

use Webbhuset\Pipeline\FunctionInterface;

class Drop implements FunctionInterface
{
    protected $amount;
    protected $current;


    public function __construct($amount)
    {
        if (!is_int($amount)) {
            throw new \InvalidArgumentException('$amount must be an int.');
        }

        if ($amount < 0) {
            throw new \InvalidArgumentException('$amount cannot be negative.');
        }

        $this->amount = (int)$amount;
    }

    public function __invoke($values, $keepState = false)
    {
        foreach ($values as $value) {
            if ($this->current < $this->amount) {
                $this->current++;
            } else {
                yield $value;
            }
        }

        if (!$keepState) {
            $this->current = 0;
        }
    }
}
