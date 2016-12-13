<?php
namespace Webbhuset\Bifrost\Core\Job\Task\Destination\Batch\Diff;

interface DiffInterface
{
    public function __construct($params);
    public function init($args);
    public function getDiff($oldData, $newData);
    public function finalize();
}
