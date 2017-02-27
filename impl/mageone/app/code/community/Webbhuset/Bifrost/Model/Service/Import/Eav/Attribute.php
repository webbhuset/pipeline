<?php

Webbhuset_Bifrost_Autoload::load();

use Webbhuset\Bifrost\Core\Component;
use Webbhuset\Bifrost\Core\Data;
use Webbhuset\Bifrost\Core\Type;
use Webbhuset\Bifrost\Core\Helper;

class Webbhuset_Bifrost_Model_Service_Import_Eav_Attribute
{
    public function createSequence($updateColumns = [])
    {
        $resource = Mage::getSingleton('core/resource');
        $adapter  = $resource->getConnection('core_write');

        $eavTable           = $resource->getTableName('eav/attribute');
        $eavColumns         = array_keys($adapter->describeTable($eavTable));

        $config = [
            'columns'       => $eavColumns,
            'primaryKey'    => 'attribute_id',
            'updateColumns' => $updateColumns,
        ];

        return new Component\Sequence\Import\Table\Flat($config);
    }

    public function createMonad($type, $afterCreateCallback = null, array $defaultValues = [])
    {
        $resource = Mage::getSingleton('core/resource');
        $adapter  = $resource->getConnection('core_write');

        $eavTable   = $resource->getTableName('eav/attribute');
        $eavColumns = array_keys($adapter->describeTable($eavTable));

        $qHeader  = 'SELECT `ea`.* FROM ';
        $qFooter  = "LEFT JOIN `{$eavTable}` AS `ea` ON `ea`.`attribute_code` = `_key`.`attribute_code`\n"
                  . "AND `ea`.`entity_type_id` = `_key`.`entity_type_id`\n";

        $queryBuilder = new Helper\Db\QueryBuilder\StaticUnion(
            ['entity_type_id', 'attribute_code'],
            $adapter,
            $qHeader,
            $qFooter
        );

        $default = array_fill_keys($eavColumns, null);
        $default = array_replace($default, [
            'entity_type_id'        => (int)$type->getId(),
        ], $defaultValues);

        $config = [
            'adapter'               => $adapter,
            'tableName'             => $eavTable,
            'primaryKey'            => 'attribute_id',
            'idQueryBuilder'        => $queryBuilder,
            'rowDefault'            => $default,
            'afterCreateCallback'   => $afterCreateCallback,
        ];

        $handler = Mage::getModel('whbifrost/resource_import_table_flatActions', $config);

        return new Component\Monad\Standard($handler);
    }

    public function createAdditionalTableSequence($type, $updateColumns = [])
    {
        $resource = Mage::getSingleton('core/resource');
        $adapter  = $resource->getConnection('core_write');

        $additionalTable    = $resource->getTableName($type->getAdditionalAttributeTable());
        $additionalColumns  = array_keys($adapter->describeTable($additionalTable));

        $config = [
            'columns'       => $additionalColumns,
            'primaryKey'    => 'attribute_id',
            'updateColumns' => $updateColumns,
        ];

        return new Component\Sequence\Import\Table\Flat($config);
    }

    public function createAdditionalTableMonad($type, $afterCreateCallback = null, array $defaultValues = [])
    {
        $resource = Mage::getSingleton('core/resource');
        $adapter  = $resource->getConnection('core_write');

        $eavTable           = $resource->getTableName('eav/attribute');
        $additionalTable    = $resource->getTableName($type->getAdditionalAttributeTable());
        $additionalColumns  = array_keys($adapter->describeTable($additionalTable));

        $queryBuilder = new Helper\Db\QueryBuilder\StaticUnion(
            ['entity_type_id', 'attribute_code'],
            $adapter,
            'die',
            ''
        );

        $qHeader  = 'SELECT `at`.*, `ea`.`attribute_id` FROM ';
        $qFooter  = "LEFT JOIN `{$eavTable}` AS `ea` ON `ea`.`attribute_code` = `_key`.`attribute_code`\n"
                  . "AND `ea`.`entity_type_id` = `_key`.`entity_type_id`\n"
                  . "LEFT JOIN {$additionalTable} AS `at` ON `at`.`attribute_id` = `ea`.`attribute_id`\n";

        $queryBuilder = new Helper\Db\QueryBuilder\StaticUnion(
            ['entity_type_id', 'attribute_code'],
            $adapter,
            $qHeader,
            $qFooter
        );

        $default = array_fill_keys($additionalColumns, null);

        $config = [
            'adapter'               => $adapter,
            'tableName'             => $additionalTable,
            'primaryKey'            => 'attribute_id',
            'idQueryBuilder'        => $queryBuilder,
            'rowDefault'            => $default,
            'afterCreateCallback'   => $afterCreateCallback,
        ];

        $handler = Mage::getModel('whbifrost/resource_import_table_flatActions', $config);

        return new Component\Monad\Standard($handler);
    }

    public function createCodeToIdMapper($type)
    {
        $resource = Mage::getSingleton('core/resource');
        $adapter  = $resource->getConnection('core_write');

        $eavTable           = $resource->getTableName('eav/attribute');

        $select = $adapter->select()
            ->from($eavTable, ['attribute_code', 'attribute_id'])
            ->where('entity_type_id = ?', $type->getId());

        $map = $adapter->fetchPairs($select);

        return new Helper\ArrayHelper\KeyMapper($map);
    }
}
