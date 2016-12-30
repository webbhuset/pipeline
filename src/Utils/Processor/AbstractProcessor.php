<?php
namespace Webbhuset\Bifrost\Core\Utils\Processor;
use \Webbhuset\Bifrost\Core\Utils as Utils;
use \Webbhuset\Bifrost\Core\BifrostException;

abstract class AbstractProcessor implements ProcessorInterface
{
    protected $log;
    protected $nextStep;


    public function __construct(Utils\Logger\LoggerInterface $log, $nextStep, $params)
    {
        if (!$nextStep instanceof Utils\Processor\ProcessorInterface
            && !$nextStep instanceof Utils\Writer\WriterInterface
        ) {
            throw new BifrostException('nextStep must implement ProcessorInterface or WriterInterface.');
        }

        $this->log         = $log;
        $this->nextStep    = $nextStep;
    }

    public function init($args)
    {
        $this->nextStep->init($args);
    }

    public function processNext($items, $onlyForCount = false)
    {
        $newItems = [];

        foreach ($items as $item) {
            try {
                $item = $this->processData($item);
                if (!empty($item)) {
                    $newItems[] = $item;
                }
            } catch (BifrostException $e) {
                $this->log->log($e->getMessage());
            }
        }

        $this->nextStep->processNext($newItems, $onlyForCount);
    }

    public function finalize($onlyForCount = false)
    {
        $this->nextStep->finalize($onlyForCount);
    }

    public function count()
    {
        return $this->nextStep->count();
    }

    abstract protected function processData($data);
}
