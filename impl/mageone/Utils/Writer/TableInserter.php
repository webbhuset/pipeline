<?php
namespace Webbhuset\Bifrost\MageOne\Utils\Writer;
use Webbhuset\Bifrost\Core\Utils\Writer\WriterInterface;
use Webbhuset\Bifrost\Core\BifrostException;


class TableInserter implements WriterInterface
{
    protected $adapter;
    protected $tableName;
    protected $columns;

    public function __construct($params)
    {
        if (!isset($params['adapter'])) {
            throw new BifrostException("Adapter parameter is not set.");
        }
        if (!$params['adapter'] instanceof \Varien_Db_Adapter_Pdo_Mysql) {
            throw new BifrostException("Adapter must be a instance of Varien_Db_Adapter_Pdo_Mysql");
        }

        if (!isset($params['table_name'])) {
            throw new BifrostException("Table name parameter is not set.");
        }
        if (!is_string($params['table_name'])) {
            throw new BifrostException("Table name must be a string");
        }

        if (!isset($params['columns'])) {
            $params['columns'] = [];
        }
        if (!is_array($params['columns'])) {
            throw new BifrostException("Columns parameter must be array.");
        }

        $this->adapter      = $params['adapter'];
        $this->tableName    = $params['table_name'];
        $this->columns      = $params['columns'];
    }

    public function init($args)
    {
    }

    public function processNext($data, $onlyForCount)
    {
        if (!$data) {
            return;
        }

        $this->adapter->insertOnDuplicate(
            $this->tableName,
            $data,
            $this->columns
        );

        return;
    }

    public function finalize($onlyForCount)
    {
    }

    public function count()
    {
    }
}
