<?php

namespace Webbhuset\Bifrost\Core\Component\Flow;

use Webbhuset\Bifrost\Core\BifrostException;
use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Webbhuset\Bifrost\Core\Component\Flow\Pipeline;
use Webbhuset\Bifrost\Core\Component\Monad\AppendContext;
use Webbhuset\Bifrost\Core\Data\ActionData\ActionDataInterface;
use Webbhuset\Bifrost\Core\Data\ActionData\EventData;

class TaskList implements ComponentInterface
{
    protected $id;
    protected $tasks;
    protected $currentTask;

    public function __construct(array $processors, $disabledTasks = [])
    {
        foreach ($processors as $idx => $processor) {
            if (!is_object($processor)) {
                throw new BifrostException("Component is not an object.");
            }
            if (!$processor instanceof ComponentInterface) {
                $class = get_class($processor);
                throw new BifrostException("Component {$class} does not implement 'ComponentInterface'");
            }
        }
        $pipelines = [];
        foreach ($processors as $taskName => $processor) {
            $pipelines[] = new Pipeline([
                $processor,
                new AppendContext($taskName),
            ]);
        }
        $this->tasks            = $pipelines;
        $this->disabledTasks    = $disabledTasks;
        $this->currentTask      = null;
    }

    public function process($items, $finalize = true)
    {
        foreach ($items as $item) {
            if ($item instanceof ActionDataInterface) {
                yield $item;
                continue;
            }

            foreach ($this->tasks as $taskName => $task) {
                if (in_array($taskName, $this->disabledTasks)) {
                    continue;
                }
                $this->currentTask = $taskName;

                yield new EventData('task_start', $item);

                $results = $task->process([$item], true);
                foreach ($results as $res) {
                    yield $res;
                }

                yield new EventData('task_done', $item);
            }
            $this->currentTask = null;
        }
    }

    /**
     * Get current task.
     *
     * @return string
     */
    public function getCurrentTask()
    {
        return $this->currentTask;
    }

    /**
     * Gets array of tasks.
     *
     * @return array
     */
    public function getTasks()
    {
        return array_keys($this->tasks);
    }
}
