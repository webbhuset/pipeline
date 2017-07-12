<?php

namespace Webbhuset\Whaskell\Convert;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class TreeToLeaves
{
    public function __invoke($tree) {
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
