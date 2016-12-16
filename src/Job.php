<?php
namespace Webbhuset\Bifrost\Core;

class Job implements Job\JobInterface
{
    protected $fetcher;
    protected $taskList;

    public function __construct($params)
    {
        $this->fetcher  = $params['fetcher'];
        $this->taskList = $params['taskList'];
    }

    public function init($args)
    {
        $filename = $this->fetcher->init($args);
        $this->taskList->init($filename, $args);
    }

    public function processNext()
    {
        $this->taskList->processOne();
    }

    public function isDone()
    {
        return $this->taskList->isDone();
    }
}
