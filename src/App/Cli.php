<?php

namespace Webbhuset\Bifrost\App;

use Webbhuset\Bifrost\App;
use Webbhuset\Bifrost\App\JobManagerInterface;
use Webbhuset\Bifrost\Helper\ArgsParser;
use Webbhuset\Bifrost\BifrostException;

class Cli
{
    /**
     * Options.
     *
     * @var array
     */
    protected $options = [
        'help' => [
            'info'          => 'Displays help for specified job(s).',
            'alias'         => ['h']
        ],
        'run' => [
            'info'          => 'Runs specified job(s).',
            'alias'         => ['r']
        ],
        'task' => [
            'info'          => 'Runs only specified task(s).',
            'alias'         => ['t']
        ],
        'skip-task' => [
            'info'          => 'Skips running specified task(s).',
            'alias'         => ['T']
        ],
        'no-draw' => [
            'info'          => 'Disables progress update drawing in terminal.',
            'alias'         => ['D']
        ],
    ];

    /**
     * Bifrost application.
     *
     * @var Webbhuset\Bifrost\App
     */
    protected $app;


    public function __construct(JobManagerInterface $jobManager)
    {
        $this->app = new App($jobManager);

        if (isset($_SERVER['REQUEST_METHOD'])) {
            die('This script is a shell script and cannot be run from a browser.');
        }
    }

    /**
     * Run.
     *
     * @return void
     */
    public function run(array $args)
    {
        $aliases    = $this->getAliasesFromOptions($this->options);
        $args       = $this->parseArgs($args, $aliases);

        $run = isset($args['run']) ? $args['run'] : false;
        if (!is_bool($run)) {
            $this->runJobs($run, $args);

            return;
        }

        $this->showGeneralHelp();

        $help = isset($args['help']) ? $args['help'] : false;
        if (is_bool($help)) {
            $this->showAvailableJobs();

            return;
        }

        if (!is_array($help)) {
            $help = [$help];
        }

        foreach ($help as $job) {
            $info = $this->app->getJobInfo($job);

            if (is_string($info)) {
                echo "\n{$info}\n";
                continue;
            }
            $this->showJobHelp($info);
        }
    }

    /**
     * Runs jobs.
     *
     * @param array $jobs
     *
     * @return void
     */
    protected function runJobs($jobs, $args)
    {
        if (!is_array($jobs)) {
            $jobs = [$jobs];
        }

        foreach ($jobs as $job) {
            try {
                $this->app->runJob($job, $args);
            } catch (BifrostException $e) {
                echo $e->getMessage();
            }
        }
    }

    /**
     * Shows general help.
     *
     * @return void
     */
    protected function showGeneralHelp()
    {
        $options = $this->optionsToString($this->options);

        echo <<<USAGE
Usage: php Cli.php [OPTION]...
Example: php Cli.php -r products

General options:
  $options


USAGE;
    }

    protected function showAvailableJobs()
    {
        $availableJobs  = $this->app->getJobList();
        $jobs = [];

        foreach ($availableJobs as $type => $typeJobs) {
            foreach ($typeJobs as $code => $model) {
                $jobs[] = "{$type}/{$code}";
            }
        }

        $jobs = implode("\n  ", $jobs);

        echo <<<JOBS
Available jobs:
  $jobs


JOBS;
    }

    /**
     * Shows help text for a job.
     *
     * @param Webbhuset_Bifrost_Model_Job_Abstract $job
     * @param string $code
     *
     * @return void
     */
    protected function showJobHelp($info)
    {
        $code           = $info['code'];
        $description    = $info['description'];
        $tasks          = implode(', ', $info['tasks']);
        $options        = $this->optionsToString($info['options']);

        echo <<<JOB
=== $code ===
Description:
  $description
Tasks:
  $tasks
Options:
  $options


JOB;
    }

    protected function optionsToString($optionArray)
    {
        $options = [];
        foreach ($optionArray as $option => $optionData) {
            $alias = [];
            if (isset($optionData['alias'])) {
                $optionAliases = $optionData['alias'];

                if (!is_array($optionAliases)) {
                    $optionAliases = [$optionAliases];
                }

                foreach ($optionAliases as $optionAlias) {
                    if (strlen($optionAlias) == 1) {
                        $alias[] = "-{$optionAlias}";
                    } else {
                        $alias[] = "--{$optionAlias}";
                    }
                }
            }

            $alias[] = "--{$option}";
            $aliasString = implode(', ', $alias);

            if (strlen($aliasString) <= 24) {
                $aliasString = str_pad($aliasString, 24);
            }

            $options[] = "{$aliasString}  {$optionData['info']}";
        }

        if (!$options) {
            $options[] = 'No options.';
        }

        return implode("\n  ", $options);
    }

    protected function getAliasesFromOptions($optionArray)
    {
        $aliases = [];
        foreach ($optionArray as $option => $optionData) {
            if (!isset($optionData['alias'])) {
                continue;
            }

            $optionAliases = $optionData['alias'];
            if (!is_array($optionAliases)) {
                $optionAliases = [$optionAliases];
            }

            foreach ($optionData['alias'] as $optionAlias) {
                $aliases[$optionAlias] = $option;
            }
        }

        return $aliases;
    }

    /**
     * Parse input arguments.
     *
     * @return $this
     */
    protected function parseArgs(array $args, array $aliases)
    {
        try {
            return ArgsParser::parseArgs($args, $aliases);
        } catch (BifrostException $e) {
            echo $e->getMessage();
            die;
        }
    }
}
