<?php
namespace Webbhuset\Bifrost\Core\Job;

interface JobInterface
{
    public function __construct($params);
    public function init($args);
    public function processNext();
    public function isDone();
    public function finalize();
}
