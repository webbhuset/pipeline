<?php

namespace Webbhuset\Whaskell\Dev;

use Webbhuset\Whaskell\AbstractFunction;

class Slice extends AbstractFunction
{
    protected $amount;
    protected $skip;


    public function __construct($amount = 1, $skip = 0)
    {
        $this->amount   = $amount;
        $this->skip     = $skip;
    }

    protected function invoke($items, $finalize = true)
    {
        $current = 0;

        foreach ($items as $item) {
            if ($current < $this->skip) {
                $current++;
            } elseif ($current < $this->amount + $this->skip) {
                $current++;

                yield $item;
            }
        }
    }
}
