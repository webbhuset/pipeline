<?php
namespace Webbhuset\Bifrost\Core\Utils\Logger;

class NullLogger implements LoggerInterface
{
    public function __construct($params = null)
    {
    }
    public function log($message, $type)
    {
    }
    public function logProgress($progress)
    {
    }
    public function finalize()
    {
    }
}
