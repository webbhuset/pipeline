<?php

use Webbhuset\Bifrost\Core as Bifrost;

/**
 * Abstract job.
 *
 * @author    Webbhuset AB <info@webbhuset.se>
 * @copyright Copyright (C) 2017 Webbhuset AB
 */
abstract class Webbhuset_Bifrost_Model_Job_Abstract
    extends Mage_Core_Model_Abstract
{
    /**
     * Job description.
     *
     * @var string
     */
    protected $_description = 'No description available.';

    /**
     * Job commands (arguments).
     *
     * Format:
     *  'COMMAND' => [
     *      'info'  => 'COMMAND DESCRIPTION',
     *      'alias' => '[ALIASES]',
     *  ];
     *
     * Example:
     *  'help' => [
     *      'info'  => 'Shows help',
     *      'alias' => ['h', '?'],
     *  ];
     *
     * @var array
     */
    protected $_commands    = [];


    /**
     * Run job.
     *
     * @param array $args
     *
     * @return void
     */
    public function run($typecode, $args)
    {
        $args = $this->_replaceAliases($args);
        $this->setArgs($args);

        list($type, $code) = explode('/', $typecode);
        $log = Mage::getModel('whbifrost/log')
            ->setType($type)
            ->setCode($code)
            ->setStartedAt(Mage::getModel('core/date')->gmtDate())
            //->setTotal($mapper->getEntityCount())
            //->setFile($file)
            ->save();
        $this->setLog($log);

        $pipeline = new Bifrost\Component\Flow\Pipeline([
            $this->_getComponent(),
            $this->_getLogMonad(),
        ]);

        $job = new Bifrost\Job($pipeline, $this->_getInput());

        while (!$job->isDone()) {
            $job->processNext();
        }

        $log->finalize();
    }

    /**
     * Get component to run.
     *
     * @return Webbhuset\Bifrost\Core\Component\ComponentInterface
     */
    protected abstract function _getComponent();

    /**
     * Get input to component.
     *
     * @return mixed
     */
    protected abstract function _getInput();

    protected function _getLogMonad()
    {
        $log = $this->getLog();
        return new Bifrost\Component\Monad\Observer([
            'task_start' => [
                function ($item, $eventName, $data) use ($log) {
                    $job    = $data['job'];
                    $task   = $data['task'];
                    $log->write("Started task '{$task}' in TaskList '{$job}'");
                }
            ],
            'task_done' => [
                function ($item, $eventName, $data) use ($log) {
                    $job    = $data['job'];
                    $task   = $data['task'];
                    $log->write("Completed task '{$task}' in TaskList '{$job}'");
                }
            ],
            'info' => [
                function ($item) use ($log) {
                    $log->write("Info", $log::TYPE_INFO);
                }
            ],
            'error' => [
                function ($error) use ($log) {
                    $log->write($error->getErrors(), $log::TYPE_ERROR);
                    $log->setErrors($log->getErrors() + 1);
                }
            ],
            'created' => [
                function ($item) use ($log) {
                    $log->write("Created item", $log::TYPE_CREATED);
                    $log->setCreated($log->getCreated() + 1);
                }
            ],
            'updated' => [
                function ($item) use ($log) {
                    $log->write("Updated item", $log::TYPE_UPDATED);
                    $log->setUpdated($log->getUpdated() + 1);
                }
            ],
            'skipped' => [
                function ($item) use ($log) {
                    $log->write("Skipped item", $log::TYPE_SKIPPED);
                    $log->setSkipped($log->getSkipped() + 1);
                }
            ],
            'not_found' => [
                function ($item) use ($log) {
                    $log->write("Item not found", $log::TYPE_NOT_FOUND);
                    $log->setNotFound($log->getNotFound() + 1);
                }
            ],
            'deleted' => [
                function ($item) use ($log) {
                    $log->write("Item deleted", $log::TYPE_DELETED);
                    $log->setDeleted($log->getDeleted() + 1);
                }
            ],
        ]);
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Get commands.
     *
     * @return array
     */
    public function getCommands()
    {
        return $this->_commands;
    }

    /**
     * Replaces aliases in args.
     *
     * @param array $args
     *
     * @return array
     */
    protected function _replaceAliases($args)
    {
        $aliases = [];
        foreach ($this->_commands as $command => $commandData) {
            if (!isset($commandData['alias'])) {
                continue;
            }

            $commandAliases = $commandData['alias'];
            if (!is_array($commandAliases)) {
                $commandAliases = [$commandAliases];
            }

            foreach ($commandAliases as $alias) {
                $aliases[$alias] = $command;
            }
        }

        foreach ($args as $arg => $value) {
            if (isset($aliases[$arg])) {
                unset($args[$arg]);

                $newArg = $aliases[$arg];

                if (!isset($args[$newArg]) || is_bool($args[$newArg])) {
                    $oldValue = [];
                } elseif (!is_array($args[$newArg])) {
                    $oldValue = [$args[$newArg]];
                } else {
                    $oldValue = $args[$newArg];
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

                $args[$newArg] = $newValue;
            }
        }

        return $args;
    }

}
