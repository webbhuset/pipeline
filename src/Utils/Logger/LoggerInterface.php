<?php
namespace Webbhuset\Bifrost\Core\Utils\Logger;

interface LoggerInterface
{
    public function __construct($params);
    public function log($message, $type);
    public function logProgress($progress);
    public function finalize();
}
