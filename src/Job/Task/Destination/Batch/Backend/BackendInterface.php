<?php
namespace Webbhuset\Bifrost\Core\Job\Task\Destination\Batch\Backend;

interface BackendInterface
{
    public function __construct($params);
    public function init($args);
    public function getOldData($keys);
    public function applyDiff($diff);
    public function finalize();
}
