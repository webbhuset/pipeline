<?php

namespace Webbhuset\Whaskell\Iterable;

use Webbhuset\Whaskell\FunctionInterface;

class Slice implements FunctionInterface
{
    protected $amount;
    protected $skip;
    protected $current;


    public function __construct($amount, $skip = 0)
    {
        $this->amount   = (int)$amount;
        $this->skip     = (int)$skip;
    }

    public function __invoke($values, $finalize = true)
    {
        foreach ($values as $value) {
            if ($this->current < $this->skip) {
                $this->current++;
            } elseif ($this->current < $this->amount + $this->skip) {
                $this->current++;

                yield $value;
            }
        }

        if ($finalize) {
            $this->current = 0;
        }
    }
}
