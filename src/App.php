<?php

namespace Webbhuset\Bifrost\Core;

use Webbhuset\Bifrost\Core\App\JobManagerInterface;

class App
{
    protected $jobManager;

    public function __construct(JobManagerInterface $jobManager)
    {
        $this->jobManager = $jobManager;
    }

    public function runJob($job, $options)
    {
        $job = $this->jobManager->getJob($job, $options);

        while (!$job->isDone()) {
            $job->processNext();
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
}
