<?php
namespace Webbhuset\Bifrost\Core\Utils\Reader;
use \Webbhuset\Bifrost\Core\Utils as Utils;
use \Webbhuset\Bifrost\Core\Utils\Processor\ProcessorInterface;
use \Webbhuset\Bifrost\Core\BifrostException;

abstract class AbstractReader implements ReaderInterface
{
    protected $logger;
    protected $nextSteps;


    public function __construct(Utils\Logger\LoggerInterface $logger, $nextSteps, $params)
    {
        foreach ($nextSteps as $nextStep) {
            if (!$nextStep instanceof \Webbhuset\Bifrost\Core\Utils\Processor\ProcessorInterface) {
                throw new BifrostException('Steps following reader must implement ProcessorInterface.');
            }
        }

        $this->logger       = $logger;
        $this->nextSteps    = $nextSteps;
    }

    public function init($args)
    {
        foreach ($this->nextSteps as $nextStep) {
            $nextStep->init($args);
        }
    }

    public function processNext()
    {
        $data = $this->getData();

        if (!$data) {
            return false;
        }

        foreach ($this->nextSteps as $nextStep) {
            $nextStep->processNext($data);
        }

        return true;
    }

    public function getEntityCount()
    {
        while ($data = $this->getData()) {
            foreach ($this->nextSteps as $nextStep) {
                $nextStep->processNext($data, true);
            }
        }

        $nextStep = $this->nextSteps[0];
        $count    = $nextStep->count();


        foreach ($this->nextSteps as $nextStep) {
            $nextStep->finalize(true);
        }

        return $count;
    }

    public function finalize()
    {
        foreach ($this->nextSteps as $nextStep) {
            $nextStep->finalize($onlyForCount);
        }
    }

    abstract protected function getData();

    public function getNextSteps()
    {
        return $this->nextSteps;
    }
}
