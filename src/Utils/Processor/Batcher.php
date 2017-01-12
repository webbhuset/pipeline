<?php
namespace Webbhuset\Bifrost\Core\Utils\Processor;
use Webbhuset\Bifrost\Core\Utils\Logger\LoggerInterface;
use Webbhuset\Bifrost\Core\BifrostException;

class Batcher extends AbstractProcessor
{
    protected $batchSize = 400;
    protected $batch     = [];

    public function __construct(LoggerInterface $log, $nextStep, $params)
    {
        parent::__construct($log, $nextStep, $params);

        if (isset($params['batch_size'])) {
            if (!is_numeric($params['batch_size'])){
                throw new BifrostException("Batch size parameter must be numeric.");
            }
            $this->batchSize = $params['batch_size'];
        }
    }

    public function init($args)
    {
        $this->batch = [];

        return parent::init($args);
    }

    public function processNext($data, $onlyForCount = false)
    {
        $this->batch = array_merge($this->batch, $data);

        if (count($this->batch) < $this->batchSize) {
            return;
        }

        foreach (array_chunk($this->batch, $this->batchSize) as $chunk) {
            foreach ($this->nextSteps as $nextStep) {
                $nextStep->processNext($chunk, $onlyForCount);
            }
        }
        $this->batch = [];

        return;
    }

    public function finalize($onlyForCount = false)
    {
        foreach (array_chunk($this->batch, $this->batchSize) as $chunk) {
            foreach ($this->nextSteps as $nextStep) {
                $nextStep->processNext($chunk, $onlyForCount);
            }
        }
        $this->batch = [];

        return parent::finalize($onlyForCount);
    }

    protected function processData($data)
    {
    }
}
