<?php

use Webbhuset\Whaskell\FunctionInterface;

class TakeEvery implements FunctionInterface
{
    protected $every;
    protected $current;


    public function __construct($every)
    {
        $this->every = (int)$every;
    }

    public function __invoke($values, $keepState = false)
    {
        foreach ($values as $value) {
            $this->current++;

            if ($this->current % $this->every == 0) {
                yield $value;
            }
        }

        if (!$keepState) {
            $this->current = 0;
        }
    }
}
