<?php

namespace Webbhuset\Bifrost\Core;

use Webbhuset\Bifrost\Core\App\JobManagerInterface;

class App
{
    protected $jobManager;
    protected $progressCallbacks = [];

    public function __construct(JobManagerInterface $jobManager)
    {
        $this->jobManager = $jobManager;
    }

    public function runJob($job, $options)
    {
        $job = $this->jobManager->getJob($job, $options);

        $lastProgress = time();

        while (!$job->isDone()) {
            $job->processNext();

            if ((time() - $lastProgress) > 2) {
                $this->updateProgress($job);
                $lastProgress = time();
            }
        }
    }

    public function getJobList()
    {
        return $this->jobManager->getJobList();
    }

    public function getJobInfo($job)
    {
        return $this->jobManager->getJobInfo($job);
    }

    public function registerProgressCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new BifrostException("Callback is not callable");
        }

        $this->progressCallbacks[] = $callback;
    }

    protected function updateProgress($job)
    {
        foreach ($this->progressCallbacks as $callback) {
            call_user_func($callback, $job);
        }
    }
}
