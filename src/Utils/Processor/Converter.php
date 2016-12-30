<?php
namespace Webbhuset\Bifrost\Core\Utils\Processor;
use \Webbhuset\Bifrost\Core\Utils\Logger\LoggerInterface;
use \Webbhuset\Bifrost\Core\Utils\ValueConverter\ValueConverterInterface;
use \Webbhuset\Bifrost\Core\BifrostException;

class Converter extends AbstractProcessor
{
    protected $fields;

    public function __construct(LoggerInterface $logger, $nextStep, $params)
    {
        parent::__construct($logger, $nextStep, $params);

        if (!isset($params['fields'])) {
            throw new BifrostException("'fields' parameter is not set.");
        }
        $this->fields = $params['fields'];
    }

    protected function processData($data)
    {
        $convertedValues = $this->convertValues($this->fields, $data);

        return $convertedValues;
    }

    protected function convertValues($fields, $data)
    {
        $result = [];
        foreach ($fields as $key => $fieldConfig) {
            if ($fieldConfig instanceof ValueConverterInterface) {
                $result[$key] = $fieldConfig->convert($data[$key]);
            } elseif (is_array($fieldConfig)) {
                $result[$key] = $this->convertValues($fieldConfig, $data[$key]);
            } else {
                $result[$key] = $data[$key];
            }
        }

        return $result;
    }
}
