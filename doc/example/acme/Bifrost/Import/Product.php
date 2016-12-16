<?php

class Product extends Webbhuset\Bifrost\Job\Factory
{
    public function createJob()
    {
        $fetcher            = new Webbhuset\Bifrost\Job\Fetcher\File($this->getFetcherConfig());
        $simpleTask         = new Achme\Import\Product\Task\Simple;
        $configurableTask   = new Achme\Import\Product\Task\Configurable;

        $taskList = $this->createTaskList([
            $simpleTask->createTask(),
            $configurableTask->createTask()
        ]);

        $job = new Webbhuset\Bifrost\Job($fetcher, $taskList);

        return $job;
    }

    public function getFetcherConfig()
    {
        return [
            'input_dir' => '/home/ftpuser/import',
            'glob_pattern' => 'products-*.csv',
        ];
    }
}
