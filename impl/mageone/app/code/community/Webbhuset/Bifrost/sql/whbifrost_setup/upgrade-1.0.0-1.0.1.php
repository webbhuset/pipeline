<?php

$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();
$tableName  = $installer->getTable('whbifrost/log_message');

$table = $installer->getConnection()
    ->newTable($tableName)
    ->addColumn('message_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'auto_increment' => true,
        'identity'       => true,
        'nullable'       => false,
        'primary'        => true,
        'unsigned'       => true,
     ), 'Message Id')
    ->addColumn('log_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'unsigned'  => true,
    ), 'Log Id')
    ->addColumn('timestamp', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => true,
    ), 'Timestamp')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, 30, array(
        'nullable'  => true,
        'default'   => '',
    ), 'Log Type')
    ->addColumn('message', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => true,
        'default'   => '',
    ), 'Message')
    ->setComment('Bifrost log message table');

$connection->createTable($table);

$installer->endSetup();
