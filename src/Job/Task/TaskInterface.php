<?php
namespace Webbhuset\Bifrost\Core\Job\Task;
use \Webbhuset\Bifrost\Core\Utils\Logger\LoggerInterface;
use Webbhuset\Bifrost\Core\Utils\Reader\ReaderInterface;

interface TaskInterface
{
    public function __construct(LoggerInterface $logger, $name, ReaderInterface $bridge);
    public function init($args);
    public function processNext();
    public function isDone();
    public function getLogger();
    public function finalize();
}
