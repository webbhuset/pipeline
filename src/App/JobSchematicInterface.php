<?php

namespace Webbhuset\Bifrost\Core\App;

use Webbhuset\Bifrost\Core\App\Job;

interface JobSchematicInterface
{
    /**
     * Create input to tasklist.
     *
     * @param array $options
     *
     * @return array|Traversable
     */
    public function createInput(array $options = []);

    /**
     * Create tasks.
     *
     * @param array $options
     *
     * @return array
     */
    public function createTasks(array $options = []);

    /**
     * Create observer.
     *
     * @param array $options
     *
     * @return array
     */
    public function createObserver(array $options = []);

    /**
     * Get job information array.
     *
     * Format:
     *  'description'   => '<DESCRIPTION>',
     *  'tasks'         => ['<TASK1>', '<TASK2>'],
     *  'options'       => [
     *      '<OPTION1>'     => [
     *          'info'          => '<OPTION-INFO>',
     *          'alias'         => ['<ALIAS>'],
     *      ],
     *  ],
     *
     * @return array
     */
    public function getJobInformation();
}
