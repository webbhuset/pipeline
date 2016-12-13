<?php
namespace Webbhuset\Bifrost\Core\Job\Task;

interface TaskInterface
{
    public function __construct($params);
    public function init($args);
    public function processNext();
    public function isDone();
    public function getProgress();
    public function finalize();
}
