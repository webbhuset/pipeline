<?php

class Job
{
    protected $fetcher;
    protected $taskList;

    public function __construct($fetcher, $taskList)
    {
        $this->fetcher  = $fetcher;
        $this->taskList = $taskList;
    }

    public function init($args)
    {
        $filename = $this->fetcher->init($args);
        $this->taskList->init($filename, $args);
    }

    public function processOne()
    {
        $this->taskList->processOne();
    }

    public function isDone()
    {
        return $this->taskList->isDone();
    }
}
