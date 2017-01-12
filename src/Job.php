<?php
namespace Webbhuset\Bifrost\Core;
use Webbhuset\Bifrost\Core\Utils\Fetcher\FetcherInterface;

class Job implements Job\JobInterface
{
    protected $fetcher;
    protected $taskList;

    public function __construct($fetcher, $taskList)
    {
        if (!isset($fetcher)) {
            throw new BifrostException("Fetcher is not set.");
        }
        if (!$fetcher instanceof FetcherInterface) {
            throw new BifrostException("Fetcher parameter must implement FetcherInterface");
        }

        if (!isset($taskList)) {
            throw new BifrostException("Task list parameter is not set.");
        }
        if (!$taskList instanceof Job\TaskList) {
            throw new BifrostException("Task list parameter must be instance of TaskList");
        }

        $this->fetcher  = $fetcher;
        $this->taskList = $taskList;
    }

    public function init($args)
    {
        $this->fetcher->init($args);
        $args['filename'] = $this->fetcher->fetch();
        $this->taskList->init($args);
    }

    public function processNext()
    {
        $this->taskList->processNext();
    }

    public function isDone()
    {
        return $this->taskList->isDone();
    }

    public function finalize()
    {
        return $this->taskList->finalize();
    }
}
