<?php
namespace Webbhuset\Bifrost\Core\Utils\Processor;
use \Webbhuset\Bifrost\Core\Utils\Logger\LoggerInterface;
use \Webbhuset\Bifrost\Core\BifrostException;

class StatementExecuter extends AbstractProcessor
{
    protected $statement;
    protected $connection;

    public function __construct(LoggerInterface $logger, $nextSteps, $params)
    {
        parent::__construct($logger, $nextSteps, $params);
        if (!isset($params['connection'])) {
            throw new BifrostException("Parameter 'connection' not set.");
        }
        if (!$params['connection'] instanceof \PDO) {
            throw new BifrostException('Connection must be instance of PDO.');
        }

        if (!isset($params['statement'])) {
            throw new BifrostException("Parameter 'statement' not set.");
        }
        if (!$params['statement'] instanceof \PDOStatement) {
            throw new BifrostException('Statement must be instance of PDOStatement.');
        }
        $this->connection = $params['connection'];
        $this->statement  = $params['statement'];
    }

    public function processNext($data, $onlyForCount)
    {
        $this->statement->execute($data['bind_values']);

        $result = [
            'bind_values' => $data['bind_values'],
            'connection'  => $this->connection,
            'statement'   => $this->statement
        ];
        foreach ($this->nextSteps as $nextStep) {
            $nextStep->processNext($result, $onlyForCount);
        }
    }

    protected function processData($data)
    {
    }
}
