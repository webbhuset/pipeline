<?php
namespace Webbhuset\Bifrost\Core\Utils\Writer;
use Webbhuset\Bifrost\Core\Utils\Processor\ProcessorInterface;

use Webbhuset\Bifrost\Core\Utils;

interface WriterInterface extends ProcessorInterface
{
    public function __construct(Utils\Logger\LoggerInterface $logger, $params);
    public function init($args);
    public function processNext($data, $onlyCount);
    public function finalize($onlyCount);
}
