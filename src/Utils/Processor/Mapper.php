<?php
namespace Webbhuset\Bifrost\Core\Utils\Processor;
use \Webbhuset\Bifrost\Core\Utils\Logger\LoggerInterface;
use \Webbhuset\Bifrost\Core\BifrostException;

class Mapper extends AbstractProcessor
{

    protected $fields;
    protected $singleCallback = false;

    public function __construct(LoggerInterface $log, $nextStep, $params)
    {
        parent::__construct($log, $nextStep, $params);

        if (!isset($params['fields'])) {
            throw new BifrostException("'fields' parameter is not set.");
        }
        $this->fields = $params['fields'];

    }
    protected function processData($data)
    {
        $mapped = $this->mapData($this->fields, $data);

        return $mapped;
    }

    protected function mapData($fields, $data)
    {
        $result = [];

        if (is_callable($fields)) {
            return call_user_func($fields, $data);
        }

        foreach ($fields as $key => $fieldConfig) {
            if (is_callable($fieldConfig)) {
                $result[$key] = call_user_func($fieldConfig, $data);
            } elseif (is_array($fieldConfig)) {
                $result[$key] = $this->mapData($fieldConfig, $data);
            } else {
                $result[$key] = $this->getValueFromData($fieldConfig, $data);
            }
        }

        return $result;
    }

    protected function getValueFromData($path, $data)
    {
        $pathArray = $this->getPathAsArray($path);
        $value     = $data;
        foreach ($pathArray as $step) {
            if (!array_key_exists($step, $value)) {
                throw new BifrostException("Could not match path '{$path}' to input data");
            }
            $value = $value[$step];
        }

        return $value;
    }

    protected function getPathAsArray($path)
    {
        $explodeChar = substr($path, 0, 1);
        $path        = substr($path, 1);

        return explode($explodeChar, $path);
    }
}
