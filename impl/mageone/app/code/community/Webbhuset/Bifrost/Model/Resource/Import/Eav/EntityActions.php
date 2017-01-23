<?php

Webbhuset_Bifrost_Autoload::load();

use Webbhuset\Bifrost\Core\Component;

class Webbhuset_Bifrost_Model_Resource_Import_Eav_EntityActions
    implements Component\Sequence\Import\Eav\Entity\ActionsInterface
{
    protected $afterCreateCallback;
    protected $entityTableName;
    protected $entityTableColumns;
    protected $adapter;
    protected $attributes;

    public function __construct($config)
    {
        $this->adapter              = $config['adapter'];
        $this->entityTableColumns   = $config['entityTableColumns'];
        $this->entityTableName      = $config['entityTableName'];
        $this->afterCreateCallback  = $config['afterCreateCallback'];
        $this->idQueryBuilder       = $config['idQueryBuilder'];

        $this->lastIdSelect = (string) $this->adapter->select()
            ->from($this->entityTableName, ['entity_id'])
            ->forUpdate(true)
            ->order('entity_id DESC')
            ->limit(1);
    }

    public function getEntityIds($entities)
    {
        $query  = $this->idQueryBuilder->buildQuery($entities);
        $ids    = $this->adapter->fetchAll($query);

        return $ids;
    }

    public function createEntities($entities)
    {
        $adapter        = $this->adapter;
        $now            = Varien_Date::now();
        $rows           = [];
        $entityTable    = $this->entityTableName;

        foreach ($entities as $entity) {
            $row = $this->entityTableColumns;
            $row = array_merge($row, array_intersect_key($entity, $this->entityTableColumns));
            if (array_key_exists('created_at', $this->entityTableColumns)) {
                $row['created_at'] = $now;
            }
            if (array_key_exists('updated_at', $this->entityTableColumns)) {
                $row['updated_at'] = $now;
            }
            $rows[] = $row;
        }

        $adapter->beginTransaction();
        $lastId = (int)$adapter->fetchOne($this->lastIdSelect);
        $adapter->insertMultiple($entityTable, $rows);

        if (is_callable($this->afterCreateCallback)) {
            call_user_func($this->afterCreateCallback, $rows, $adapter, $entityTable);
        }

        $select         = $adapter->select()->from($entityTable)->where('entity_id > ?', $lastId);
        $insertedIds    = $adapter->fetchAll($select);
        $adapter->commit();

        return $insertedIds;
    }

    public function insertAttributeValues($rows, $table)
    {
        $adapter    = $this->adapter;
        $insertRows = [];
        $updatedIds = [];

        $adapter->insertOnDuplicate($table, $rows, ['value']);
        if (array_key_exists('updated_at', $this->entityTableColumns)) {
            $now = Varien_Date::now();
            $adapter->update($this->entityTableName, ['updated_at' => $now], ['entity_id IN (?)' => $updatedIds]);
        }
    }

    public function fetchAttributeValues($entityIds, $table, $attributeIds)
    {
        $adapter    = $this->adapter;

        $select     = $adapter->select()
            ->from($table)
            ->where('entity_id IN (?)', $entityIds)
            ->where('attribute_id IN (?)', $attributeIds);

        $rows       = $adapter->fetchAll($select);

        return $rows;
    }
}
