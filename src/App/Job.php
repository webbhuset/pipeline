<?php

namespace Webbhuset\Bifrost\Core\App;

use Traversable;
use Webbhuset\Bifrost\Core\BifrostException;
use Webbhuset\Bifrost\Core\App\JobSchematicInterface;
use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Webbhuset\Bifrost\Core\Component\Action;
use Webbhuset\Bifrost\Core\Component\Flow;

use Webbhuset\Bifrost\Core\Component\Dev;

/**
 * Job.
 *
 * @author    Webbhuset AB <info@webbhuset.se>
 * @copyright Copyright (C) 2017 Webbhuset AB
 */
class Job
{
    protected $taskList;
    protected $generator;
    protected $isDone       = false;

    /**
     * Constructor.
     *
     * @param Webbhuset\Bifrost\Core\Component\ComponentInterface $component
     * @param string $code
     * @param array|Traversable $input
     *
     * @return void
     */
    public function __construct(JobSchematicInterface $schematic, $code, array $options = [])
    {
        $options        = $this->replaceAliases($schematic, $options);
        $input          = $schematic->createInput($options);
        $tasks          = $schematic->createTasks($options);
        $observer       = $schematic->createObserver($options);

        if (!is_array($input) && !$input instanceof Traversable) {
            $input = [$input];
        }

        if (isset($options['task'])) {
            $runTask = $options['task'];
            if (!is_array($runTask)) {
                $runTask = [$runTask];
            }
            $tasksToSkip = array_diff(array_keys($tasks), $runTask);
        } elseif (isset($options['skip-task'])) {
            $skipTask = $options['skip-task'];
            if (!is_array($skipTask)) {
                $skipTask = [$skipTask];
            }
            $tasksToSkip = $skipTask;
        } else {
            $tasksToSkip = [];
        }

        $this->taskList = new Flow\TaskList($tasks, $tasksToSkip);

        $pipelineArray = [
            new Action\Event('jobStart', ['code' => $code]),
            new Action\SideEffect('jobBefore'),
            new Flow\Fork([
                $this->taskList,
                new Flow\Pipeline([
                    new Action\SideEffect('jobAfter'),
                    new Action\Event('jobDone'),
                ])
            ]),
        ];

        if ($observer) {
            $pipelineArray[] = $observer;
        }

        $pipeline = new Flow\Pipeline($pipelineArray);
        $this->generator = $pipeline->process($input);
    }

    /**
     * Replaces option aliases.
     *
     * @param JobSchematicInterface $schematic
     * @param array $options
     *
     * @return array
     */
    protected function replaceAliases(JobSchematicInterface $schematic, array $options)
    {
        $jobInfo = $schematic->getJobInformation();

        if (!isset($jobInfo['options'])) {
            return $options;
        }

        $aliases = [];
        foreach ($jobInfo['options'] as $option => $optionData) {
            if (!isset($optionData['alias'])) {
                continue;
            }

            if (!is_array($optionData['alias'])) {
                $aliases[$optionData['alias']] = $option;
                continue;
            }

            foreach ($optionData['alias'] as $alias) {
                $aliases[$alias] = $option;
            }
        }

        foreach ($options as $option => $value) {
            if (!isset($aliases[$option])) {
                continue;
            }

            unset($options[$option]);

            $alias = $aliases[$option];

            if (!isset($options[$alias]) || is_bool($options[$alias])) {
                $oldValue = [];
            } elseif (!is_array($options[$alias])) {
                $oldValue = [$options[$alias]];
            } else {
                $oldValue = $options[$alias];
            }

            if (is_bool($value)) {
                $value = [];
            } elseif (!is_array($value)) {
                $value = [$value];
            }

            $newValue = array_merge($oldValue, $value);

            if (!$newValue) {
                $newValue = true;
            } elseif (count($newValue) == 1) {
                $newValue = reset($newValue);
            }

            $options[$alias] = $newValue;
        }

        return $options;
    }

    /**
     * Get current task.
     *
     * @return array
     */
    public function getCurrentTask()
    {
        return $this->taskList->getCurrentTask();
    }

    /**
     * Process next. Returns true if there's more to process, else false.
     *
     * @return bool
     */
    public function processNext()
    {
        if ($this->isDone) {
            return false;
        }

        $this->generator->next();

        if (!$this->generator->valid()) {
            $this->isDone = true;
        }

        return !$this->isDone;
    }

    /**
     * Is processing done?
     *
     * @return bool
     */
    public function isDone()
    {
        return $this->isDone;
    }
}
