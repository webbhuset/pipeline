<?php

use Webbhuset\Pipeline\FunctionInterface;

class TakeEvery implements FunctionInterface
{
    protected $amount;
    protected $current;


    public function __construct($amount)
    {
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
