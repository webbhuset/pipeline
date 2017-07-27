<?php

namespace Webbhuset\Whaskell\Convert;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Webbhuset\Whaskell\AbstractFunction;

class TreeToLeaves extends AbstractFunction
{
    protected function invoke($tree, $finalize = true)
    {
        $it = new RecursiveIteratorIterator(
            new RecursiveArrayIterator(
                $tree,
                RecursiveArrayIterator::CHILD_ARRAYS_ONLY
            )
        );

        foreach ($it as $leaf) {
            yield $leaf;
        }
    }
}
