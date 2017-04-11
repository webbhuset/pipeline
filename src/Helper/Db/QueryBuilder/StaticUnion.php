<?php

namespace Webbhuset\Bifrost\Helper\Db\QueryBuilder;

use Webbhuset\Bifrost\Helper\Db;
use Webbhuset\Bifrost\BifrostException;

class StaticUnion
{
    protected $adapter;
    protected $keys;
    protected $keyAlias = '_key';

    public function __construct(array $keys, $adapter, $header, $footer)
    {
        $this->keyAlias = $adapter->quoteIdentifier('_key');
        $orderBy = "\nORDER BY {$this->keyAlias}.`pos` ASC\n";
        $this->adapter      = $adapter;
        $this->keys         = $keys;
        $this->queryHeader  = $header;
        $this->queryFooter  = $footer.$orderBy;
    }

    public function buildQuery($entities)
    {
        $keys       = $this->keys;
        $adapter    = $this->adapter;
        $select     = '';
        $rows       = [];
        $pos        = 0;

        foreach ($entities as $entity) {
            $row = [];
            if ($pos == 0) {
                $row[] = "{$pos} AS `pos`";
            } else {
                $row[] = $pos;
            }
            foreach ($keys as $key) {
                $value = $adapter->quote((string)$entity[$key]);
                if ($pos == 0) {
                    $alias = $adapter->quoteIdentifier($key);
                    $row[] = "{$value} AS {$alias}";
                } else {
                    $row[] = $value;
                }
            }
            $pos += 1;
            $rows[] = '  SELECT ' . implode(',', $row);
        }

        $union  = "(\n" . implode(" UNION ALL\n", $rows) . "\n) AS {$this->keyAlias}\n";
        $select = $this->queryHeader . $union . $this->queryFooter;

        return $select;
    }
}
