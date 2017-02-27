<?php

$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();

$table = $installer->getTable('eav/attribute_label');
$select = $connection->select()
    ->from($table, [
        'attribute_label_id',
        'attribute_id',
        'store_id',
        'count' => new Zend_Db_Expr('COUNT(attribute_label_id)')
    ])
    ->group('attribute_id')
    ->group('store_id')
    ->having('count > 1');

$rows = $connection->fetchAll($select);

foreach ($rows as $row) {
    $connection->delete($table, [
        'attribute_label_id    != ?' => $row['attribute_label_id'],
        'attribute_id           = ?' => $row['attribute_id'],
        'store_id               = ?' => $row['store_id'],
    ]);
}

$connection->dropIndex(
    $table,
    $installer->getIdxName(
        $table,
        ['attribute_id', 'store_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
    )
);

$connection->addIndex(
    $table,
    $installer->getIdxName(
        $table,
        ['attribute_id', 'store_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    ['attribute_id', 'store_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->endSetup();
