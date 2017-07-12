<?php

namespace Webbhuset\Whaskell\Convert;

use Webbhuset\Whaskell\Dispatch\Data\DataInterface;
use Webbhuset\Whaskell\WhaskellException;

class RowsToTree
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

    public function __invoke($items)
    {
        foreach ($items as $rows) {
            if ($rows instanceof DataInterface) {
                yield $rows;
                continue;
            }

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
