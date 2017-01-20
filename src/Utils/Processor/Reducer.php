<?php
namespace Webbhuset\Bifrost\Core\Utils\Processor;
use \Webbhuset\Bifrost\Core\Utils\Logger\LoggerInterface;
use \Webbhuset\Bifrost\Core\BifrostException;

class Reducer extends AbstractProcessor
{
    protected $callback;
    protected $initialValue;

    public function __construct(LoggerInterface $log, $nextStep, $params)
    {
        parent::__construct($log, $nextStep, $params);

        if (!isset($params['callback'])) {
            throw new BifrostException("'callback' parameter is not set.");
        }
        if (!is_callable($params['callback'])) {
            throw new BifrostException("'callback' parameter is not callable.");
        }

        $this->callback     = $params['callback'];
        $this->initialValue = isset($params['initial'])
                            ? $params['initial']
                            : null;
    }

    public function processNext($items, $onlyForCount = false)
    {
        $newItems = $this->processData($items);

        if (empty($newItems)) {
            return;
        }

        foreach ($this->nextSteps as $nextStep) {
            $nextStep->processNext($newItems, $onlyForCount);
        }
    }

    protected function processData($items)
    {
        return array_reduce($items, $this->callback, $this->initialValue);
    }
}
