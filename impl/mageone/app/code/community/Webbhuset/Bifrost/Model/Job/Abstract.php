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
    public function run($args)
    {
        $args = $this->_replaceAliases($args);
        $this->setArgs($args);

        $job = new Bifrost\Job($this->_getComponent(), $this->_getInput());

        while (!$job->isDone()) {
            $job->processNext();
        }
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
