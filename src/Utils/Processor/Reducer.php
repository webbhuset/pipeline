<?php
namespace Webbhuset\Bifrost\Core\Utils\Processor;
use \Webbhuset\Bifrost\Core\Utils\Logger\LoggerInterface;
use \Webbhuset\Bifrost\Core\BifrostException;

class Reducer extends AbstractProcessor
{
    protected $callback;

    public function __construct(LoggerInterface $log, $nextStep, $params)
    {
        parent::__construct($log, $nextStep, $params);

        if (!isset($params['callback'])) {
            throw new BifrostException("'callback' parameter is not set.");
        }
        if (!is_callable($params['callback'])) {
            throw new BifrostException("'callback' parameter is not callable.");
        }

        $this->callback = $params['fields'];

    }

    public function processNext($items, $onlyForCount = false)
    {
        $newItems = array_reduce($items, $this->callback);

        if (empty($newItems)) {
            return;
        }

        foreach ($this->nextSteps as $nextStep) {
            $nextStep->processNext($newItems, $onlyForCount);
        }
    }

     protected function processData($data)
     {

     }
}
