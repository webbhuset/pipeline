<?php

namespace Webbhuset\Bifrost\App;

use Exception;
use Traversable;
use Webbhuset\Bifrost\BifrostException;
use Webbhuset\Bifrost\App\JobSchematicInterface;
use Webbhuset\Bifrost\Component\ComponentInterface;
use Webbhuset\Bifrost\Component\Action;
use Webbhuset\Bifrost\Component\Dev;
use Webbhuset\Bifrost\Component\Flow;
use Webbhuset\Bifrost\Data;

/**
 * Job.
 *
 * @author    Webbhuset AB <info@webbhuset.se>
 * @copyright Copyright (C) 2017 Webbhuset AB
 */
class Job
{
    protected $taskList;
    protected $observer;
    protected $generator;
    protected $isDone = false;

    /**
     * Constructor.
     *
     * @param Webbhuset\Bifrost\Component\ComponentInterface $component
     * @param string $code
     * @param array|Traversable $input
     *
     * @return void
     */
    public function __construct(JobSchematicInterface $schematic, $code, array $options = [])
    {
        $options        = $this->replaceAliases($schematic, $options);
        $preprocessing  = $schematic->createPreprocessing($options);
        $postprocessing = $schematic->createPostprocessing($options);
        $tasks          = $schematic->createTasks($options);
        $observer       = $schematic->createObserver($options);
        $tasksToSkip    = $this->getTasksToSkip($tasks, $options);

        if (!is_array($input) && !$input instanceof Traversable) {
            $input = [$input];
        }

        $this->taskList = new Flow\TaskList($tasks, $tasksToSkip);
        $this->observer = new Flow\Pipeline($observer);

        $pipeline = new Flow\Pipeline([
            new Flow\Pipeline($preprocessing),
            new Flow\Fork([
                $this->taskList,
                new Flow\Pipeline($postprocessing),
            ]),
            $this->observer,
        ]);

        $jobStartEvent = new Data\ActionData\EventData(
            'jobStart',
            [],
            ['code' => $code, 'options' => $options]
        );
        iterator_to_array($this->observer->process([$jobStartEvent]));

        $input              = $schematic->createInput($options);
        $this->generator    = $pipeline->process($input);
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
     * Get tasks that should be skipped.
     *
     * @param array $tasks
     * @param array $options
     *
     * @return array
     */
    protected function getTasksToSkip($tasks, $options)
    {
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

        return $tasksToSkip;
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

        try {
            $this->generator->next();
        } catch (Exception $e) {
            $events = [
                new Data\ActionData\ErrorData([], $e->__toString()),
                new Data\ActionData\EventData('taskDone', []),
                new Data\ActionData\EventData('jobDone', []),
            ];
            iterator_to_array($this->observer->process($events));
            throw $e;
        }

        if (!$this->generator->valid()) {
            $this->isDone = true;
            $jobDoneEvent = new Data\ActionData\EventData('jobDone', []);
            iterator_to_array($this->observer->process([$jobDoneEvent]));
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
