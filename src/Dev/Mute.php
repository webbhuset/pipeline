<?php

namespace Webbhuset\Whaskell\Dev;

use Webbhuset\Whaskell\AbstractFunction;

class Mute extends AbstractFunction
{
    protected function invoke($items, $finalize = true)
    {
        return [];
    }
}
