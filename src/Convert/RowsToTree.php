<?php

namespace Webbhuset\Whaskell\Convert;

use Webbhuset\Whaskell\AbstractFunction;
use Webbhuset\Whaskell\WhaskellException;

class RowsToTree extends AbstractFunction
{
    protected $dimensions;

    public function __construct(array $dimensions)
    {
        if (count($dimensions) < 2) {
            throw new WhaskellException("A tree needs at least two dimensions.");
        }
        $this->valueKey     = array_pop($dimensions);
        $this->dimensions   = $dimensions;
    }

    protected function invoke($items, $finalize = true)
    {
        foreach ($items as $rows) {
            $tree = [];

            foreach ($rows as $row) {
                $node = &$tree;
                $isRowBroken = false;
                foreach ($this->dimensions as $column) {
                    if (!array_key_exists($column, $row)) {
                        $isRowBroken = true;
                        break;
                    }
                    $value = $row[$column];
                    if (!array_key_exists($value, $node)) {
                        $node[$value] = [];
                    }
                    $node = &$node[$value];
                }
                if ($isRowBroken || !array_key_exists($this->valueKey, $row)) {
                    continue;
                }
                $node = $row[$this->valueKey];
            }

            yield $tree;
        }
    }
}
