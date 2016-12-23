<?php
namespace Webbhuset\Bifrost\Core\Utils\Writer;
use \Webbhuset\Bifrost\Core\Utils;
use \Webbhuset\Bifrost\Core\BifrostException;

abstract class AbstractWriter implements WriterInterface
{
    protected $log;


    public function __construct(Utils\Log\LogInterface $log, $params)
    {
        $this->log = $log;
    }
}
