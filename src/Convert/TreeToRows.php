<?php

namespace Webbhuset\Whaskell\Convert;

use Webbhuset\Whaskell\Dispatch\Data\DataInterface;
use Webbhuset\Whaskell\WhaskellException;

class TreeToRows
{
    protected $staticRows = [[]];
    protected $columns;

    public function __construct(array $columns, array $treeDimensions, array $staticValues = [])
    {
        foreach ($columns as $column) {
            $staticIdx = array_key_exists($column, $staticValues);
            $treeIdx   = array_search($column, $treeDimensions);

            if ($staticIdx && ($treeIdx !== false)) {
                throw new WhaskellException("Column '{$column}' is found in both tree dimensions and static values.");
            }

            if (!$staticIdx && ($treeIdx === false)) {
                throw new WhaskellException("Column '{$column}' is not found in either tree dimensions or static values");
            }

            $this->columns[$column] = $staticIdx
                                    ? $column
                                    : $treeIdx;
        }

        if ($staticValues) {
            $generator = [[]];

            foreach ($staticValues as $column => $values) {
                $generator = $this->permuteValues($values, $column, $generator);
            }

            $this->staticRows = iterator_to_array($generator);
        }
    }

    public function __invoke($items)
    {
        foreach ($items as $tree) {
            if ($tree instanceof DataInterface) {
                yield $tree;
                continue;
            }

            if (empty($tree)) {
                $treeGen = [[]];
            } else {
                $treeGen = $this->flattenTree($tree);
            }

            foreach ($treeGen as $treeRow) {
                foreach ($this->staticRows as $staticRow) {
                    $row = [];
                    foreach ($this->columns as $column => $idx) {
                        if (array_key_exists($idx, $treeRow)) {
                            $value = $treeRow[$idx];
                        } elseif (array_key_exists($idx, $staticRow)) {
                            $value = $staticRow[$idx];
                        } else {
                            $row = null;
                            break;
                        }
                        $row[$column] = $value;
                    }
                    yield $row;
                }
            }
        }
    }

    protected function flattenTree($tree, $row = [])
    {
        foreach ($tree as $key => $value) {
            if (is_array($value)) {
                $genenerator = $this->flattenTree($value, array_merge($row, [$key]));
                foreach ($genenerator as $result) {
                    yield $result;
                }
            } else {
                $result = array_merge($row, [$key]);
                $result = array_merge($result, [$value]);
                yield $result;
            }
        }
    }

    protected function permuteValues($values, $column, $rows)
    {
        if (!is_array($values)) {
            $values = [$values];
        }
        foreach ($rows as $row) {
            foreach ((array)$values as $value) {
                $row[$column] = $value;
                yield $row;
            }
        }
    }
}
