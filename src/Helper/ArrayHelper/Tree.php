<?php

namespace Webbhuset\Bifrost\Core\Helper\ArrayHelper;

use RecursiveIteratorIterator;
use RecursiveArrayIterator;

class Tree
{
    /**
     * Returns a tree with nodes from A that are present in B.
     *
     * @param array $a
     * @param array $b
     * @static
     * @access public
     * @return array
     */
    public static function diffRecursive($a, $b)
    {
        $diff = [];

        foreach ($a as $key => $value) {
            if (!array_key_exists($key, $b)) {
                $diff[$key] = $value;
            } else {
                if (is_array($value)) {
                    $value = self::diffRecursive($value, $b[$key]);
                    if ($value) {
                        $diff[$key] = $value;
                    }
                } elseif ($value != $b[$key]) {
                    $diff[$key] = $value;
                }
            }
        }

        return $diff;
    }

    public static function buildRecursiveMapper($root, $treeChildren, $wildcard = '*')
    {
        $children = [];

        foreach ($treeChildren as $key => $value) {
            if (is_array($value)) {
                $mapper = self::buildRecursiveMapper($lastMapper, $value);
                $children[$lastKey] = $mapper;
            } else {
                $children[$key] = $value;
                $lastMapper     = $value;
                $lastKey        = $key;
            }
            $lastKey    = $key;
            $lastMapper = $value;
        }

        return $root->addChildren($children, $wildcard);
    }

    public static function getLeaves($tree)
    {
        return iterator_to_array(
            new RecursiveIteratorIterator(
                new RecursiveArrayIterator(
                    $tree,
                    RecursiveArrayIterator::CHILD_ARRAYS_ONLY
                )
            ),
            false
        );
    }
}
