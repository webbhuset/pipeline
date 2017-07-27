<?php

namespace Webbhuset\Whaskell\Dev;

use Webbhuset\Whaskell\AbstractFunction;

class Export extends AbstractFunction
{
    protected function invoke($items, $finalize = true)
    {
        foreach ($items as $item) {
            echo var_export($item) . ",\n";
            yield $item;
        }
    }
}
