<?php
namespace Webbhuset\Bifrost\Core\Job;

class TaskList
{
    protected $tasks  = [];
    protected $isDone = false;

    public function __construct(array $tasks)
    {
        foreach ($tasks as $task) {
            if (!$task instanceof Task\TaskInterface) {
                throw new BifrostException('Task must implement TaskInterface.');
            }
        }

        $this->tasks = $tasks;
    }

    public function init($args)
    {
        foreach ($this->tasks as $task) {
            $task->init($args);
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

    public function finalize()
    {
        foreach ($this->tasks as $task) {
            $task->finalize();
        }
    }
}
