<?php
namespace Webbhuset\Bifrost\Core\Utils\Processor;
use \Webbhuset\Bifrost\Core\Utils as Utils;
use \Webbhuset\Bifrost\Core\BifrostException;

abstract class AbstractProcessor implements ProcessorInterface
{
    protected $log;
    protected $nextChain;


    public function __construct(Utils\Log\LogInterface $log, $nextChain, $params)
    {
        if (!$nextChain instanceof Utils\Processor\ProcessorInterface
            && !$nextChain instanceof Utils\Writer\WriterInterface
        ) {
            throw new BifrostException('NextChain must implement ProcessorInterface or WriterInterface.');
        }

        $this->log          = $log;
        $this->nextChain    = $nextChain;
    }

    public function init($args)
    {
        $this->nextChain->init($args);
    }

    public function processNext($items)
    {
        $newItems = [];

        foreach ($items as $item) {
            try {
                $item       = $this->processData($item);
                $newItems[] = $item;
            } catch (BifrostException $e) {
                $this->log($e->getMessage());
            }
        }

        $nextChain->processNext($newItems);
    }

    public function finalize()
    {
        $this->nextChain->finalize();
    }

    abstract protected function processData($data);
}
