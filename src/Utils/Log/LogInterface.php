<?php
namespace Webbhuset\Bifrost\Core\Utils\Log;

interface LogInterface
{
    public function __construct($params);
    public function log($message, $type);
    public function logProgress($progress);
    public function finalize();
}
