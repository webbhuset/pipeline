<?php

namespace Webbhuset\Bifrost\Core\Component\Db;

use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Webbhuset\Bifrost\Core\BifrostException;

class TableToTree implements ComponentInterface
{
    protected $dimensions;

    public function __construct(array $dimensions)
    {
        if (count($dimensions) < 2) {
            throw new BifrostException("A tree needs at least two dimensions.");
        }
        $this->valueKey     = array_pop($dimensions);
        $this->dimensions   = $dimensions;
    }

    public function process($items)
    {
        foreach ($items as $key => $rows) {
            if (is_string($key)) {
                yield $key => $rows;
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
