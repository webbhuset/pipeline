<?php

namespace Webbhuset\Bifrost\Core\Component\Flow;

use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Webbhuset\Bifrost\Core\BifrostException;
use Webbhuset\Bifrost\Core\Data;

class TaskList implements ComponentInterface
{
    protected $id;
    protected $forks;

    public function __construct(array $processors, $id, $disabledTasks = [])
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
        $this->forks            = $processors;
        $this->id               = $id;
        $this->disabledTasks    = $disabledTasks;
    }

    public function process($items, $finalize = true)
    {
        foreach ($items as $key => $item) {
            if (is_string($key)) {
                yield $key => $item;
                continue;
            }
            foreach ($this->forks as $taskName => $fork) {
                if (in_array($taskName, $this->disabledTasks)) {
                    continue;
                }
                $eventData = ['job' => $this->id, 'task' => $taskName];
                $transport = new Data\Reference($item, 'task_start', $eventData);
                yield 'event' => $transport;
                $results = $fork->process([$item], true);
                foreach ($results as $key => $res) {
                    if (is_string($key)) {
                        yield $key => $res;
                        continue;
                    }
                    yield $res;
                }
                $transport = new Data\Reference($item, 'task_done', $eventData);
                yield 'event' => $transport;
            }
        }
    }
}
