<?php

class Cli
{
    public function run($job)
    {
        $job->init($args);

        while (!$job->isDone()) {
            $this->readInput();
            $this->updateUi();
            $job->processOne();
        }
    }
}
