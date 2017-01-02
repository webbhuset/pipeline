<?php
namespace Webbhuset\Bifrost\Core\Utils\Writer;
use Webbhuset\Bifrost\Core\Utils\Processor\ProcessorInterface;

use Webbhuset\Bifrost\Core\Utils;

interface WriterInterface
{
    public function __construct(Utils\Logger\LoggerInterface $log, $params);
    public function init($args);
    public function processNext($data);
    public function finalize();
}
