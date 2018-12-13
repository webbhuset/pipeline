<?php

use Webbhuset\Pipeline\FunctionInterface;

class TakeEvery implements FunctionInterface
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
            $this->current++;

            if ($this->current % $this->amount == 0) {
                yield $value;
            }
        }

        if (!$keepState) {
            $this->current = 0;
        }
    }
}
