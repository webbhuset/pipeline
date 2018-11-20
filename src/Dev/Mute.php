<?php

namespace Webbhuset\Whaskell\Dev;

use Webbhuset\Whaskell\AbstractFunction;

class Mute implements FunctionInterface
{
    public function __invoke($items, $finalize = true)
    {
        foreach ($items as $item) {
            if (false) {
                yield null;
            }
        }
    }
}
