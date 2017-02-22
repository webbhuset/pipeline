<?php

$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();

$logTableName = $installer->getTable('whbifrost/log');

$logTable = $installer->getConnection()
    ->newTable($logTableName)
    ->addColumn('log_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        'unsigned'  => true,
    ], 'Log ID')
    ->addColumn('started_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => true,
    ], 'Created At')
    ->addColumn('completed_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable'  => true,
    ], 'Completed At')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
        'nullable'  => true,
        'default'   => '',
    ], 'Type')
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 32, [
        'nullable'  => true,
        'default'   => '',
    ], 'Code')
    ->addColumn('total', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'nullable'  => true,
        'unsigned'  => true,
    ], 'Total')
    ->addColumn('created', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'default'   => 0,
    ], 'Created')
    ->addColumn('updated', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'default'   => 0,
    ], 'Updated')
    ->addColumn('skipped', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'default'   => 0,
    ], 'Skipped')
    ->addColumn('not_found', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'default'   => 0,
    ], 'Not Found')
    ->addColumn('errors', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'default'   => 0,
        'unsigned'  => true,
    ], 'Errors')
    ->addColumn('message', Varien_Db_Ddl_Table::TYPE_TEXT, null, [
        'nullable'  => true,
        'default'   => '',
    ], 'Message')
    ->setComment('Bifrost Log Table');

$installer->getConnection()->createTable($logTable);

$installer->endSetup();
