<?php
namespace Webbhuset\Bifrost\Core\Job;
use \Webbhuset\Bifrost\Core\Utils\Logger\LoggerInterface;
use Webbhuset\Bifrost\Core\Utils\Reader\ReaderInterface;

class Task implements Task\TaskInterface
{
    protected $bridge;
    protected $logger;
    protected $name;

    protected $isDone = false;

    public function __construct(LoggerInterface $logger, $name, ReaderInterface $bridge)
    {
        $this->logger    = $logger;
        $this->name      = $name;
        $this->bridge    = $bridge;
    }

    public function init($args)
    {
        $this->$bridge->init($args);
        $this->logger->init($args);

        $count = $this->$bridge->getEntityCount();
        $this->logger->setTotal($count);
    }

    public function processNext()
    {
        $hasMoreData  = $this->bridge->processNext();
        $this->isDone = !$hasMoreData;

        return $hasMoreData;
    }

    public function finalize()
    {
        $this->$bridge->finalize();
    }

    public function isDone()
    {
        return $this->isDone;
    }

    public function getLogger()
    {
        return $this->logger;
    }
}
