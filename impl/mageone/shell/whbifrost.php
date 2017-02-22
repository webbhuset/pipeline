<?php

require 'abstract.php';

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
  -r, --run [JOB]           Runs specified job(s).
  -h, --help [JOB]          Displays help for specified job(s).
  -p, --peasant             Job(s) are run as peasant jobs instead of directly.
  -D, --no-draw             Disables progress update drawing in terminal.


USAGE;
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
        foreach ($job->getCommands() as $command => $info) {
            if (strlen($command) > 24) {
                $commands[] = "  {$command}  {$info}";
            } else {
                $commands[] = '  ' . str_pad($command, 26) . $info;
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
        $current = null;

        foreach ($_SERVER['argv'] as $arg) {
            $match = [];
            if (preg_match('/^--([\w\d_-]+)$/', $arg, $match)) {
                $current = $match[1];
                if (!isset($this->_args[$current])) {
                    $this->_args[$current] = true;
                }

                continue;
            }

            if (preg_match('/^-([\w\d_]+)$/', $arg, $match)) {
                $split = str_split($match[1]);
                foreach ($split as $char) {
                    foreach ($this->_aliases as $alias => $command) {
                        if ($char == $alias) {
                            $char = $command;
                            break;
                        }
                    }
                    $current = $char;
                    if (!isset($this->_args[$current])) {
                        $this->_args[$current] = true;
                    }
                }

                continue;
            }

            if ($current) {
                if (is_bool($this->_args[$current])) {
                    $this->_args[$current] = $arg;

                    continue;
                }

                if (is_string($this->_args[$current])) {
                    $this->_args[$current] = [$this->_args[$current]];
                }

                $this->_args[$current][] = $arg;
            } else if (preg_match('#^([\w\d_]{1,})$#', $arg, $match)) {
                echo "Unexpected argument '{$arg}'. Did you mean '--{$arg}'?\n";
                die;
            }
        }

        return $this;
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
