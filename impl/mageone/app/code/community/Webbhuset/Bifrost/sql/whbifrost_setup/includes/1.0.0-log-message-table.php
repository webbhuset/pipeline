<?php

$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();
$tableName  = $installer->getTable('whbifrost/log_message');

$table = $installer->getConnection()
    ->newTable($tableName)
    ->addColumn('message_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'auto_increment'    => true,
        'identity'          => true,
        'nullable'          => false,
        'primary'           => true,
        'unsigned'          => true,
     ], 'Message Id')
    ->addColumn('log_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'          => false,
        'unsigned'          => true,
    ], 'Log Id')
    ->addColumn('timestamp', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'          => true,
    ], 'Timestamp')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, 30, [
        'nullable'          => true,
        'default'           => '',
    ], 'Log Type')
    ->addColumn('message', Varien_Db_Ddl_Table::TYPE_TEXT, null, [
        'nullable'          => true,
        'default'           => '',
    ], 'Message')
    ->setComment('Bifrost log message table');

$connection->createTable($table);

$installer->endSetup();
