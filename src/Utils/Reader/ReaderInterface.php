<?php
namespace Webbhuset\Bifrost\Core\Utils\Reader;
use \Webbhuset\Bifrost\Core\Utils\Logger\LoggerInterface;

interface ReaderInterface
{
    public function __construct(LoggerInterface $log, $nextStep, $params);
    public function init($args);
    public function getEntityCount();
    public function processNext();
    public function finalize();
}
