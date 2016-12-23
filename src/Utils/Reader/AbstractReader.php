<?php
namespace Webbhuset\Bifrost\Core\Utils\Reader;
use \Webbhuset\Bifrost\Core\Utils as Utils;
use \Webbhuset\Bifrost\Core\BifrostException;

abstract class AbstractReader implements ReaderInterface
{
    protected $log;
    protected $nextChain;


    public function __construct(Utils\Log\LogInterface $log, $nextChain, $params)
    {
        if (!$nextChain instanceof Utils\Processor\ProcessorInterface
            && !$nextChain instanceof Utils\Writer\WriterInterface
        ) {
            throw new BifrostException('NextChain must implement ProcessorInterface or WriterInterface');
        }

        $this->log          = $log;
        $this->nextChain    = $nextChain;
    }

    public function processNext()
    {
        $data = $this->getData();

        if (!$data) {
            return false;
        }

        $nextChain->processNext($data);

        return true;
    }

    abstract protected function getData();
}
