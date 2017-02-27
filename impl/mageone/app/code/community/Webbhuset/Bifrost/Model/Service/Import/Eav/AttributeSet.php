<?php

Webbhuset_Bifrost_Autoload::load();

use Webbhuset\Bifrost\Core\Component;
use Webbhuset\Bifrost\Core\Data;
use Webbhuset\Bifrost\Core\Type;
use Webbhuset\Bifrost\Core\Helper;

class Webbhuset_Bifrost_Model_Service_Import_Eav_AttributeSet
{
    public function createSequence(array $columns = [])
    {
        if (empty($columns)) {
            $columns = [
                'attribute_set_id',
                'attribute_set_name',
                'sort_order',
            ];
        }
        $config = [
            'columns' => $columns,
            'primaryKey'    => 'attribute_set_id',
            'updateColumns' => [
            ]
        ];

        return new Component\Sequence\Import\Table\Flat($config);
    }

    public function createMonad($afterCreateCallback = null, array $setDefault = [])
    {
        $resource = Mage::getSingleton('core/resource');
        $adapter  = $resource->getConnection('core_write');
        $table    = $resource->getTableName('eav/attribute_set');

        $qHeader  = 'SELECT `eas`.`attribute_set_id` FROM ';
        $qFooter  = "LEFT JOIN {$table} AS `eas` ON `eas`.`attribute_set_name` = `_key`.`attribute_set_name`\n"
                  . "AND `eas`.`entity_type_id` = 4\n";

        $queryBuilder = new Helper\Db\QueryBuilder\StaticUnion(['attribute_set_name'], $adapter, $qHeader, $qFooter);

        $default = array_fill_keys(array_keys($adapter->describeTable($table)), null);
        $default = array_replace($default, [
            'entity_type_id'        => 4,
            'is_content_type'       => 0,
            'show_direct_create'    => 0,
            'can_be_standalone'     => 1,
            'content_type_code'     => '',
        ], $setDefault);

        $config = [
            'adapter'               => $adapter,
            'tableName'             => $table,
            'primaryKey'            => 'attribute_set_id',
            'idQueryBuilder'        => $queryBuilder,
            'rowDefault'            => $default,
            'afterCreateCallback'   => $afterCreateCallback,
        ];

        $handler = Mage::getModel('whbifrost/resource_import_table_flatActions', $config);

        return new Component\Monad\Standard($handler);
    }

    public function createCloneAttributeSetFunction($sourceSetId, $entityTypeId)
    {
        $cloneSetId     = (int)$sourceSetId;
        $entityTypeId   = (int)$entityTypeId;

        return function($rows, $insertedIds, $adapter) use ($cloneSetId, $entityTypeId) {
            $cloneGroups = $adapter->prepare(
                "INSERT INTO `eav_attribute_group`\n"
                . "(attribute_set_id, attribute_group_name, sort_order, default_id)\n"
                . "SELECT :new_id, attribute_group_name, sort_order, default_id\n"
                . "FROM eav_attribute_group where attribute_set_id = {$cloneSetId}"
            );

            $cloneAttributes = $adapter->prepare(
                "INSERT INTO eav_entity_attribute"
                . " SELECT null AS entity_attribute_id, {$entityTypeId} AS entity_type_id, :new_id AS attribtue_set_id,"
                . " eag_dst.attribute_group_id, eea.attribute_id, eea.sort_order FROM eav_entity_attribute AS eea"
                . " INNER JOIN eav_attribute_group AS eag_src ON eag_src.attribute_group_id = eea.attribute_group_id"
                . " INNER JOIN eav_attribute_group AS eag_dst ON eag_dst.attribute_group_name = eag_src.attribute_group_name AND eag_dst.attribute_set_id = :new_id"
                 . " WHERE eea.attribute_set_id = {$cloneSetId};"
            );

            foreach ($insertedIds as $set) {
                $newSetId = $set['attribute_set_id'];
                $cloneGroups->execute([
                    'new_id' => $newSetId,
                ]);
                $cloneAttributes->execute([
                    'new_id' => $newSetId,
                ]);
            }
        };
    }
}
