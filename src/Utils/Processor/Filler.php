<?php
namespace Webbhuset\Bifrost\Core\Utils\Processor;
use \Webbhuset\Bifrost\Core\Utils\Logger\LoggerInterface;
use \Webbhuset\Bifrost\Core\BifrostException;

class Filler extends AbstractProcessor
{
    protected $backend = [];

    public function __construct(LoggerInterface $log, $nextStep, $params)
    {
        parent::__construct($log, $nextStep, $params);
        if (!isset($params['backend'])) {
            throw new BifrostException("'Backend' parameter is not set.");
        }
        $this->backend = $params['backend'];
    }

    protected function processData($data)
    {
        $valuesToAdd          = $this->backend->getData($data);
        $dataAndBackendValues = $this->fillValues($data, $valuesToAdd);

        return $dataAndBackendValues;
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

}
