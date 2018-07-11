<?php

namespace Webbhuset\Whaskell\Dev;

use Webbhuset\Whaskell\AbstractFunction;

class Slice extends AbstractFunction
{
    protected $amount;
    protected $skip;
    protected $current;


    public function __construct($amount = 1, $skip = 0)
    {
        $this->amount   = $amount;
        $this->skip     = $skip;
    }

    protected function invoke($items, $finalize = true)
    {
        foreach ($items as $item) {
            if ($this->current < $this->skip) {
                $this->current++;
            } elseif ($this->current < $this->amount + $this->skip) {
                $this->current++;

                yield $item;
            }
        }
    }
}
