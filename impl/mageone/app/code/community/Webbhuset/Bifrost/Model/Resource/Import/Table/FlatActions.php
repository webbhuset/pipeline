<?php

Webbhuset_Bifrost_Autoload::load();

use Webbhuset\Bifrost\Core\Component;

class Webbhuset_Bifrost_Model_Resource_Import_Table_FlatActions
    implements Component\Sequence\Import\Table\Flat\ActionsInterface
{
    protected $afterCreateCallback;
    protected $tableName;
    protected $primaryKey;
    protected $adapter;

    public function __construct($config)
    {
        $this->adapter              = $config['adapter'];
        $this->tableName            = $config['tableName'];
        $this->primaryKey           = $config['primaryKey'];
        $this->afterCreateCallback  = $config['afterCreateCallback'];
        $this->idQueryBuilder       = $config['idQueryBuilder'];
        $this->rowDefault           = $config['rowDefault'];

        $this->lastIdSelect = (string) $this->adapter->select()
            ->from($this->tableName, [$this->primaryKey])
            ->forUpdate(true)
            ->order($this->primaryKey . ' DESC')
            ->limit(1);
    }

    public function getOldData(array $entities)
    {
        $query  = $this->idQueryBuilder->buildQuery($entities);
        $ids    = $this->adapter->fetchAll($query);

        return $ids;
    }

    public function insertNewRows(array $rows)
    {
        $adapter        = $this->adapter;

        $insertRows = [];

        foreach ($rows as $row) {
            $newRow         = array_replace($this->rowDefault, array_intersect_key($row, $this->rowDefault));
            $insertRows[]   = $newRow;
        }

        $adapter->beginTransaction();

        try {
            $lastId = (int)$adapter->fetchOne($this->lastIdSelect);
            $adapter->insertMultiple($this->tableName, $insertRows);

            $select         = $adapter->select()->from($this->tableName, [$this->primaryKey])
                                ->where("{$this->primaryKey} > ?", $lastId);
            $insertedIds    = $adapter->fetchAll($select);

            if (is_callable($this->afterCreateCallback)) {
                call_user_func($this->afterCreateCallback, $rows, $insertedIds, $adapter);
            }

            $adapter->commit();
        } catch (Exception $e) {
            $adapter->rollback();
            return [];
        }

        return $insertedIds;
    }

    public function updateRows(array $rows, array $updateColumns)
    {
        $adapter = $this->adapter;

        $updateRows = [];

        foreach ($rows as $row) {
            $newRow         = array_intersect_key($row, $this->rowDefault);
            $updateRows[]   = $newRow;
        }

        $adapter->insertOnDuplicate($this->tableName, $updateRows, $updateColumns);
    }
}
