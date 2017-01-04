<?php
namespace Webbhuset\Bifrost\Core\Utils\Processor;
use \Webbhuset\Bifrost\Core\Utils as Utils;
use \Webbhuset\Bifrost\Core\BifrostException;

abstract class AbstractProcessor implements ProcessorInterface
{
    protected $logger;
    protected $nextSteps;


    public function __construct(Utils\Logger\LoggerInterface $logger, $nextSteps, $params)
    {
        foreach ($nextSteps as $nextStep) {
            if (!$nextStep instanceof Utils\Processor\ProcessorInterface) {
                throw new BifrostException('Following steps must implement ProcessorInterface.');
            }
        }

        $this->logger      = $logger;
        $this->nextSteps   = $nextSteps;
    }

    public function init($args)
    {
        foreach ($this->nextSteps as $nextStep) {
            $nextStep->init($args);
        }
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
                $this->logger->log($e->getMessage());
            }
        }

        foreach ($this->nextSteps as $nextStep) {
            $nextStep->processNext($newItems, $onlyForCount);
        }
    }

    public function finalize($onlyForCount = false)
    {
        foreach ($this->nextSteps as $nextStep) {
            $nextStep->finalize($onlyForCount);
        }
    }

    public function count()
    {
        $count = 0;
        foreach ($this->nextSteps as $nextStep) {
            $count += $nextStep->count();
        }
        return $count;
    }

    abstract protected function processData($data);
}
