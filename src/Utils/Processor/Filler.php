<?php
namespace Webbhuset\Bifrost\Core\Utils\Processor;
use \Webbhuset\Bifrost\Core\Utils\Logger\LoggerInterface;
use \Webbhuset\Bifrost\Core\BifrostException;

class Filler extends AbstractProcessor
{

    protected $fields;
    protected $fillVaules = [];

    public function __construct(LoggerInterface $log, $nextStep, $params)
    {
        parent::__construct($log, $nextStep, $params);
        if (!isset($params['fill_values'])) {
            throw new BifrostException("'fill_values' parameter is not set.");
        }
        $this->fillVaules = $params['fill_values'];
    }

    protected function processData($data)
    {
        $dataAndDefaults = $this->addDefaultValues($data, $this->fillVaules);

        return $dataAndDefaults;
    }

    protected function addDefaultValues($data, $defaultArray)
    {
        foreach ($defaultArray as $key => $defaultValue) {
            if (isset($data[$key])) {
                if (!is_array($data[$key])) {
                    continue;
                }
            } else {
                $data[$key] = [];
            }

            if (is_array($defaultValue) && is_callable($defaultValue)) {
                $data[$key] = call_user_func($defaultValue, $data);
            } elseif (is_array($defaultValue)) {
                $data[$key] = $this->addDefaultValues($data[$key], $defaultValue);
            } else {
                $data[$key] = $defaultValue;
            }
        }

        return $data;
    }

}
