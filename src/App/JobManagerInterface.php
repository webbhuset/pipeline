<?php

namespace Webbhuset\Bifrost\App;

interface JobManagerInterface
{
    /**
     * Get available jobs.
     *
     * @return array
     */
    public function getJobList();

    /**
     * Get job.
     *
     * @param $string $code
     * @param array $options
     *
     * @return Webbhuset\Bifrost\App\Job
     */
    public function getJob($code, $options);

    /**
     * Get job info array. Returns string on error.
     *
     * Keys:
     *  'code'          => string
     *  'description'   => string
     *  'tasks'         => array
     *  'options'       => array
     *
     * @param string $code
     *
     * @return array|string
     */
    public function getJobInfo($code);
}
