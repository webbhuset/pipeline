<?php

namespace Webbhuset\Whaskell\Convert;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Webbhuset\Whaskell\AbstractFunction;

class TreeToLeaves extends AbstractFunction
{
    protected function invoke($items, $finalize = true)
    {
        foreach ($items as $item) {
            $it = new RecursiveIteratorIterator(
                new RecursiveArrayIterator(
                    $item,
                    RecursiveArrayIterator::CHILD_ARRAYS_ONLY
                )
            );

            foreach ($it as $leaf) {
                yield $leaf;
            }
        }
    }
}
