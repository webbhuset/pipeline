<?php

namespace Webbhuset\Bifrost\Core\Helper\Eav\QueryBuilder;

use Webbhuset\Bifrost\Core\Helper\Db;
use Webbhuset\Bifrost\Core\BifrostException;

class IdsFromEntities
{
    protected $useStoreId;
    protected $adapter;
    protected $queryHeader;
    protected $queryFooter;
    protected $keys;
    protected $entityTable;

    public function __construct(array $keys, $entityTable, array $attributes, $adapter, $useStoreId = true)
    {
        $this->adapter      = $adapter;
        $this->keys         = $keys;
        $this->entityTable  = $entityTable;
        $this->userStoreId  = $useStoreId;
        $attributesByCode   = [];

        foreach ($attributes as $attribute) {
            $code = $attribute->getCode();
            $attributesByCode[$code] = $attribute;
        }

        list ($header, $footer) = $this->buildQueryHeaderAndFooter($keys, $attributesByCode);
        $this->queryHeader = $header;
        $this->queryFooter = $footer;
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
                if (!array_key_exists($key, $entity)) {
                    throw new BifrostException("Key '{$key}' does not exist in entity.");
                }
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

        $union = "(\n" . implode(" UNION ALL\n", $rows) . "\n) AS `_k`\n";

        $select = $this->queryHeader . $union . $this->queryFooter;

        return $select;
    }

    protected function buildQueryHeaderAndFooter($keys, $attributesByCode)
    {
        $adapter = $this->adapter;
        list ($staticKeys, $eavKeys) = $this->sortKeyAttributes($keys, $attributesByCode);
        list ($entityJoins, $column) = $this->assembleStaticKeys($staticKeys);
        list ($eavJoins, $column) = $this->assembleEavKeys($eavKeys, $column);
        $staticJoin = '';
        if (!$staticKeys) {
            $staticJoin = $this->joinStaticTable($column);
        }
        $queryHeader = "SELECT MAX({$column}) AS `entity_id`, `e`.`attribute_set_id` FROM\n";
        $queryFooter = $entityJoins
                     . $eavJoins
                     . $staticJoin
                     . "GROUP BY `_k`.`pos`\n"
                     . "ORDER BY `_k`.`pos` ASC";

        return [$queryHeader, $queryFooter];
    }

    protected function assembleStaticKeys($staticKeys)
    {
        $adapter            = $this->adapter;
        $condition          = [];
        $column             = '';
        $queryFooter        = '';

        foreach ($staticKeys as $attribute) {
            $entityTable    = $adapter->quoteIdentifier($attribute->getTable());
            $alias          = $adapter->quoteIdentifier($attribute->getCode());
            $condition[]    = "`e`.{$alias} = `_k`.{$alias}";
        }

        if ($condition) {
            $column        = '`e`.`entity_id`';
            $condition     = implode(' AND ', $condition);
            $queryFooter   = $this->formatLeftJoin($entityTable, '`e`', $condition);
        }

        return [$queryFooter, $column];
    }

    protected function joinStaticTable($column)
    {
        return $this->formatLeftJoin($this->entityTable, 'e', "{$column} = `e`.`entity_id`");
    }

    protected function assembleEavKeys($eavKeys, $column)
    {
        $adapter = $this->adapter;
        $joinRow = '';

        foreach ($eavKeys as $attribute) {
            $alias    = $adapter->quoteIdentifier('at_'. $attribute->getCode());
            $table    = $adapter->quoteIdentifier($attribute->getTable());
            $cond     = $this->getEavJoinCondition($column, $alias, $attribute);
            $column   = "{$alias}.`entity_id`";
            $joinRow .= $this->formatLeftJoin($table, $alias, $cond);
        }

        return [$joinRow, $column];
    }

    protected function formatLeftJoin($table, $alias, $cond)
    {
        return sprintf("LEFT JOIN %s AS %s ON %s\n", $table, $alias, $cond);
    }

    protected function sortKeyAttributes($keys, $attributesByCode)
    {
        $staticKeys = [];
        $eavKeys    = [];

        foreach ($keys as $key) {
            if (!isset($attributesByCode[$key])) {
                throw new BifrostException("Attribute {$key} is not found.");
            }
            $attribute = $attributesByCode[$key];
            if ($attribute->getBackendType() == 'static') {
                $staticKeys[] = $attribute;
            } else {
                $eavKeys[] = $attribute;
            }
        }

        return [$staticKeys, $eavKeys];
    }

    protected function getEavJoinCondition($column, $alias, $attribute)
    {
        $adapter    = $this->adapter;
        $id         = (int)$attribute->getId();
        $key        = $adapter->quoteIdentifier($attribute->getCode());
        $cond       = [];

        if ($column) {
            $cond[] = "{$alias}.`entity_id` = {$column}";
        }

        $cond[] = "{$alias}.`attribute_id` = {$id}";

        if ($this->useStoreId) {
            $cond[] = "{$alias}.`store_id` = 0";
        }

        $cond[] = "{$alias}.`value` = `_k`.{$key}";
        $cond   = implode(' AND ', $cond);

        return $cond;
    }
}
