<?php
namespace Webbhuset\Bifrost\Core\Utils\Logger;

class NullLogger implements LoggerInterface
{
    public function __construct($params = null)
    {
    }
    public function log($message = null, $type = null)
    {
    }
    public function logProgress($progress = null)
    {
    }
    public function finalize()
    {
    }
}
