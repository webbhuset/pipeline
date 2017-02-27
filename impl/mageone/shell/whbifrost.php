<?php

require 'abstract.php';
use \Webbhuset\Bifrost\Core\Helper\ArgsParser;
use \Webbhuset\Bifrost\Core\BifrostException;

/**
 * Bifrost CLI script.
 *
 * @author    Webbhuset AB <info@webbhuset.se>
 * @copyright Copyright (C) 2017 Webbhuset AB
 */
class Webbhuset_Bifrost_Shell
    extends Mage_Shell_Abstract
{
    /**
     * Aliases.
     *
     * @var array
     */
    protected $_aliases = [
        'r' => 'run',
        'h' => 'help',
        'p' => 'peasant',
        'D' => 'no-draw',
    ];


    /**
     * Run.
     *
     * @return void
     */
    public function run()
    {
        $run = $this->getArg('run');
        if ($run && !is_bool($run)) {
            $this->_runJobs($run);

            return;
        }

        $this->_showGeneralHelp();

        $help = $this->getArg('help');
        if (!$help || is_bool($help)) {
            $this->_showAvailableJobs();

            return;
        }

        if (!is_array($help)) {
            $help = [$help];
        }

        $jobModels = $this->_getJobModels($help);
        foreach ($jobModels as $code => $job) {
            $this->_showJobHelp($job, $code);
        }
    }

    /**
     * Runs jobs.
     *
     * @param array $jobs
     *
     * @return void
     */
    protected function _runJobs($jobs)
    {
        $jobModels = $this->_getJobModels($jobs);

        if ($this->getArg('peasant')) {
            foreach ($jobModels as $model) {
                $peasantArgs = [
                    'params' => [
                        'job'       => $model,
                        'args'      => $this->_args,
                    ]
                ];
                Mage::helper('whpeasant')->addJob(
                    'whbifrost_job',
                    1,
                    $peasantArgs
                );
            }
        } else {
            foreach ($jobModels as $model) {
                $model->run($this->_args);
            }
        }
    }

    /**
     * Retrieves all job models matching specified jobs.
     *
     * @param array $jobs
     *
     * @return array
     */
    protected function _getJobModels($jobs) {
        if (!is_array($jobs)) {
            $jobs = [$jobs];
        }

        $availableJobs  = Mage::helper('whbifrost')->getJobs();
        $jobModels      = [];
        foreach ($jobs as $job) {
            if (strpos($job, '/') !== false) {
                $exploded   = explode('/', $job);
                $type       = array_shift($exploded);
                $code       = implode('/', $exploded);
                if (isset($availableJobs[$type][$code])) {
                    $jobModels[$job] = $this->_getJobModel($availableJobs[$type][$code], $job);
                } else {
                    echo "Unknown job '{$job}.'\n";
                    die();
                }
            } else {
                $matching = [];
                foreach ($availableJobs as $type => $codes) {
                    if (isset($codes[$job])) {
                        $matching["{$type}/{$job}"] = $codes[$job];
                    }
                }

                if (!$matching) {
                    echo "Unknown job '{$job}.'\n";
                    die();
                }

                if (count($matching) > 1) {
                    printf(
                        "Ambiguous job '%s'. Did you mean '%s'?\n",
                        $job,
                        implode("' or '", array_keys($matching))
                    );
                    die();
                }

                $code = reset(array_keys($matching));
                $jobModels[$code] = $this->_getJobModel(reset($matching), $code);
            }
        }

        return $jobModels;
    }

    /**
     * Retrieves a job instance.
     *
     * @param string $job
     * @param string $code
     *
     * @return Webbhuset_Bifrost_Model_Job_Abstract
     */
    protected function _getJobModel($job, $code)
    {
        $model = Mage::getModel($job);

        if (!$model) {
            printf(
                "Unknown model '%s' for job '%s'.\n",
                $job,
                $code
            );
            die();
        }

        if (!$model instanceof Webbhuset_Bifrost_Model_Job_Abstract) {
            printf(
                "Class '%s' for job '%s' does not extend 'Webbhuset_Bifrost_Model_Job_Abstract'.\n",
                get_class($model),
                $code
            );
            die();
        }

        return $model;
    }

    /**
     * Shows general help.
     *
     * @return void
     */
    protected function _showGeneralHelp()
    {
        echo <<<USAGE
Usage: php whbifrost.php [OPTION]...
Example: php whbifrost -r products

General options:
  -r, --run                 Runs specified job(s).
  -h, --help                Displays help for specified job(s).
  -p, --peasant             Job(s) are run as peasant jobs instead of directly.
  -D, --no-draw             Disables progress update drawing in terminal.


USAGE;
    }

    protected function _showAvailableJobs()
    {
        $availableJobs  = Mage::helper('whbifrost')->getJobs();
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
    protected function _showJobHelp($job, $code)
    {
        $commands = [];
        foreach ($job->getCommands() as $command => $commandData) {
            $alias = [];
            if (isset($commandData['alias'])) {
                $commandAliases = $commandData['alias'];

                if (!is_array($commandAliases)) {
                    $commandAliases = [$commandAliases];
                }

                foreach ($commandAliases as $commandAlias) {
                    if (strlen($commandAlias) == 1) {
                        $alias[] = "-{$commandAlias}";
                    } else {
                        $alias[] = "--{$commandAlias}";
                    }
                }
            }

            $alias[] = "--{$command}";
            $alias = implode(', ', $alias);

            if (strlen($alias) > 24) {
                $commands[] = "  {$alias}  {$commandData['info']}";
            } else {
                $commands[] = '  ' . str_pad($alias, 26) . $commandData['info'];
            }
        }

        if (!$commands) {
            $commands[] = '  No options.';
        }

        $description = $job->getDescription();
        $commands = implode("\n", $commands);
        echo <<<JOB

$code description:
  $description

$code options:
$commands


JOB;
    }

    /**
     * Parse input arguments.
     *
     * @return $this
     */
    protected function _parseArgs()
    {
        try {
            $args = $_SERVER['argv'];
            array_shift($args);
            $this->_args = ArgsParser::parseArgs($args, $this->_aliases);
        } catch (BifrostException $e) {
            echo $e->getMessage();
            die;
        }
    }

    /**
     * Prevents parent from hijacking h and help.
     *
     * @return void
     */
    protected function _showHelp()
    {
        return;
    }
}

$shell = new Webbhuset_Bifrost_Shell;
$shell->run();
