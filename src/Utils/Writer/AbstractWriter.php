<?php
namespace Webbhuset\Bifrost\Core\Utils\Writer;
use Webbhuset\Bifrost\Core\Utils;
use Webbhuset\Bifrost\Core\BifrostException;

abstract class AbstractWriter implements WriterInterface
{
    protected $logger;

    public function __construct(Utils\Logger\LoggerInterface $logger, $params)
    {
        $this->logger = $logger;
    }

    public function getNextSteps()
    {
        return false;
    }

}
