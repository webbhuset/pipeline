<?php
namespace Webbhuset\Bifrost\Core\Job;

class TaskList
{
    protected $progress;
    protected $tasks = [];
    protected $currentTaskIdx = 0;

    protected $isDone = false;

    public function __construct(array $tasks)
    {
        $this->tasks = $tasks;
        $this->progress = new Progress;
    }

    public function init($filename, $args)
    {
        foreach ($this->tasks as $task) {
            $task->init($filename, $args);
        }
    }

    public function processNext()
    {
        $task = $this->getCurrentTask();

        if (!$task) {
            $this->isDone = true;
            return;
        }

        $task->processNext();

        return true;
    }

    public function getCurrentTask()
    {
        foreach ($this->tasks as $task) {
            if ($task->isDone()) {
                continue;
            }

            return $task;
        }

        return false;
    }

    public function isDone()
    {
        return $this->isDone;
    }
}
