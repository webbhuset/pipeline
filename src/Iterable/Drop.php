<?php

namespace Webbhuset\Whaskell\Iterable;

use Webbhuset\Whaskell\FunctionInterface;

class Take implements FunctionInterface
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
