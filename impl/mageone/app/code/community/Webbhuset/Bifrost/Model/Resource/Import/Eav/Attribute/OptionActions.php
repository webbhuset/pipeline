<?php

Webbhuset_Bifrost_Autoload::load();

class Webbhuset_Bifrost_Model_Resource_Import_Eav_Attribute_OptionActions
{
    protected $adapter;
    protected $optionTable;
    protected $optionValueTable;

    public function __construct($config)
    {
        $this->adapter              = $config['adapter'];
        $this->optionTable          = $config['optionTable'];
        $this->optionValueTable     = $config['optionValueTable'];
        $this->queryBuilder         = $config['queryBuilder'];

        $this->lastIdSelect = (string) $this->adapter->select()
            ->from($this->optionTable, ['option_id'])
            ->forUpdate(true)
            ->order('option_id DESC')
            ->limit(1);
    }

    public function getOldData($rows)
    {
        $query  = $this->queryBuilder->buildQuery($rows);
        $result = $this->adapter->fetchAll($query);

        return $result;
    }

    public function insertNewRows($rows) {
        $adapter    = $this->adapter;
        $insertRows = [];

        foreach ($rows as $row) {
            $insertRows[] = [
                'attribute_id'  => $row['attribute_id'],
                'sort_order'    => $row['sort_order'],
            ];
        }

        $adapter->beginTransaction();
        $lastId = (int)$adapter->fetchOne($this->lastIdSelect);
        $adapter->insertMultiple($this->optionTable, $insertRows);

        $select         = $adapter->select()->from($this->optionTable, ['option_id'])->where('option_id > ?', $lastId);
        $insertedIds    = $adapter->fetchAll($select);

        $valueRows = [];

        foreach ($insertedIds as $idx => $row) {
            $valueRow                = $row;
            $valueRow['store_id']    = 0;
            $valueRow['value']       = $rows[$idx]['label'][0];
            $valueRows[] = $valueRow;
        }

        $adapter->insertMultiple($this->optionValueTable, $valueRows);
        $adapter->commit();

        return $insertedIds;
    }

    public function updateRows($rows, $updateColumns) {
        $adapter    = $this->adapter;
        $updateRows = [];

        foreach ($rows as $row) {
            $updateRows[] = [
                'option_id'     => $row['option_id'],
                'attribute_id'  => $row['attribute_id'],
                'sort_order'    => $row['sort_order'],
            ];
        }

        $adapter->insertOnDuplicate($this->optionTable, $updateRows, ['sort_order']);
    }
}
