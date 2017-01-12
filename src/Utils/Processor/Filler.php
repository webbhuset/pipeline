<?php
namespace Webbhuset\Bifrost\Core\Utils\Processor;
use \Webbhuset\Bifrost\Core\Utils\Logger\LoggerInterface;
use \Webbhuset\Bifrost\Core\BifrostException;

class Filler extends AbstractProcessor
{
    protected $backend  = [];
    protected $collector;
    protected $keySpec;

    public function __construct(LoggerInterface $log, $nextStep, $params)
    {
        parent::__construct($log, $nextStep, $params);
        if (!isset($params['backend'])) {
            throw new BifrostException("'Backend' parameter is not set.");
        }
        $this->backend   = $params['backend'];
        $writers         = $this->getWriters($params['backend']);
        if (count($writers)!==1) {
            throw new BifrostException("The backend chain must have exactly one writer.");
        }

        if (!method_exists($writers[0], 'getData')) {
            throw new BifrostException("The collector must have a 'getData' method.");
        }

        $this->collector = $writers[0];
        if (!isset($params['key_specification'])) {
            throw new BifrostException("'key_specification' parameter is not set.");
        }
        $this->keySpec = $params['key_specification'];
    }

    public function processNext($items, $onlyForCount = false)
    {
        $this->backend->processNext($items, $onlyForCount);
        $backendData = $this->collector->getData();

        $newItem = [];
        foreach ($items as $item) {
            $key         = $this->getKey($item);
            $valuesToAdd = $this->getValueFromKey($backendData, $key);
            $newItem     = $this->fillValues($item, $valuesToAdd);

            if (!empty($item)) {
                $newItems[] = $newItem;
            }
        }

        foreach ($this->nextSteps as $nextStep) {
            $nextStep->processNext($newItems, $onlyForCount);
        }
    }

    protected function getKey($item)
    {
        $keySpec = $this->keySpec;
        if (is_array($keySpec)
            && count($keySpec) === 1
            && is_string($keySpec[0]))
        {
            return [$this->getValueFromKey($item, $keySpec)];
        }

        if (is_callable($keySpec)) {
            return call_user_func($keySpec, $item);
        }
    }

    protected function getValueFromKey($data, $key)
    {
        foreach ($key as $keyPart) {
            if (!isset($data[$keyPart])) {
                return [];
            }
            $data = $data[$keyPart];
        }

        return $data;
    }

    protected function fillValues($data, $valuesToAdd)
    {
        foreach ($valuesToAdd as $key => $addValue) {
            if (isset($data[$key])) {
                if (!is_array($data[$key])) {
                    continue;
                }
            } else {
                $data[$key] = [];
            }

            if (is_array($addValue) && is_callable($addValue)) {
                $data[$key] = call_user_func($addValue, $data);
            } elseif (is_array($addValue)) {
                $data[$key] = $this->fillValues($data[$key], $addValue);
            } else {
                $data[$key] = $addValue;
            }
        }

        return $data;
    }

    protected function processData($data)
    {
    }

    protected function getWriters($processor)
    {
        if (!$processor->getNextSteps()) {
            return [$processor];
        }

        $writers = [];
        foreach ($processor->getNextSteps() as $step) {
            $writers += $this->getWriters($step);
        }

        return $writers;
    }
}
