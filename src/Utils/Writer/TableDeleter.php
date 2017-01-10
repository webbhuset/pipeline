<?php
namespace Webbhuset\Bifrost\Core\Utils\Writer;
use Webbhuset\Bifrost\Core\Utils\Writer\WriterInterface;
use Webbhuset\Bifrost\Core\Utils\Logger\LoggerInterface;
use Webbhuset\Bifrost\Core\BifrostException;

class TableDeleter implements WriterInterface
{
    protected $adapter;
    protected $tableName;
    protected $columns;
    protected $statement;
    protected $logger;

    public function __construct(LoggerInterface $logger, $params)
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
            throw new BifrostException("Columns parameter must be set.");
        }
        if (!is_array($params['columns'])) {
            throw new BifrostException("Columns parameter must be array.");
        }

        $this->logger       = $logger;
        $this->adapter      = $params['adapter'];
        $this->tableName    = $params['table_name'];
        $this->columns      = $params['columns'];

        $adapter = $this->adapter;
        $where   = $this->getWhere();
        $sql     = "DELETE FROM "
            . $adapter->quoteIdentifier($this->tableName, true)
            . " WHERE ( $where );";
        $this->statement = $this->adapter->prepare($sql);
    }

    public function init($args)
    {
    }

    protected function getWhere()
    {
        $where = [];
        foreach ($this->columns as $columnName) {
            if (!preg_match("/^[a-zA-Z0-9_\$]+$/", $columnName)) {
                throw new BifrostException("Illegal characters in column name.");
            }

            $where[] = "{$columnName} = :{$columnName}";
        }

        return implode(' AND ', $where);
    }

    public function processNext($data, $onlyForCount)
    {
        if (!$data) {
            return;
        }

        foreach ($data as $row) {
            $bind = $this->getBind($row);
            $this->statement->execute($bind);
        }

        return;
    }

    protected function getBind($row)
    {
        $bind = [];
        foreach ($this->columns as $columnName) {
            if (!isset($row[$columnName])) {
                $bind[":{$columnName}"] = null;
            } else {
                $bind[":{$columnName}"] = $row[$columnName];
            }
        }

        return $bind;
    }

    public function finalize($onlyForCount)
    {
    }

    public function count()
    {
    }

    public function getNextSteps()
    {
        return false;
    }
}
