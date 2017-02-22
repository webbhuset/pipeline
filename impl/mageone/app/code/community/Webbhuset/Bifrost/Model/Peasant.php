<?php

/**
 * Peasant worker.
 *
 * @author    Webbhuset AB <info@webbhuset.se>
 * @copyright Copyright (C) 2017 Webbhuset AB
 */
class Webbhuset_Bifrost_Model_Peasant
    extends Webbhuset_Peasant_Model_Worker
{
    /**
     * Run job.
     *
     * @return void
     */
    protected function _run()
    {
        $peasantJob     = $this->getJob();
        $params         = $peasantJob->getParams();
        $bifrostJob     = $params->getJob();
        $bifrostArgs    = $params->getArgs();

        $bifrostJob->run($bifrostArgs);
    }
}
