<?php

namespace Webbhuset\Whaskell\Dev;

use Webbhuset\Whaskell\AbstractFunction;

class Mute extends AbstractFunction
{
    protected function invoke($items, $finalize = true)
    {
        foreach ($items as $item) {
            if (false) {
                yield null;
            }
        }
    }
}
