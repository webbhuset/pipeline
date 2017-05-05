<?php

namespace Webbhuset\Bifrost\Data;

use Webbhuset\Bifrost\BifrostException;
use Webbhuset\Bifrost\Type\TypeInterface;

class Attribute implements AttributeInterface
{
    protected $data;
    protected $optionsValueMap  = [];

    public function __construct(array $data)
    {
        $required = ['code', 'table', 'type'];

        foreach ($required as $key) {
            if (!isset($data[$key])) {
                throw new BifrostException("Constructor key '{$key}' is missing.");
            }
        }

        if (!$data['type'] instanceof TypeInterface) {
            throw new BifrostException("Type must implement 'Webbhuset\Bifrost\Type\TypeInterface'.");
        }

        $default = [
            'defaultValue'  => null,
            'options'       => null,
        ];

        $data = array_replace($default, $data);

        $this->data = $data;

        if (isset($data['options']) && is_array($data['options'])) {
            foreach ($data['options'] as $key => $value) {
                $this->optionsValueMap[mb_strtoupper($value)] = $this->getType()->cast($key);
            }
        }
    }

    public function getCode()
    {
        return $this->data['code'];
    }

    public function getTable()
    {
        return $this->data['table'];
    }

    public function getType()
    {
        return $this->data['type'];
    }

    public function getDefaultValue()
    {
        return $this->data['defaultValue'];
    }

    public function usesOptions()
    {
        return !is_null($this->data['options']);
    }

    public function mapOptionValue($value)
    {
        if (is_array($value)) {
            foreach ($value as &$v) {
                $v = $this->mapOptionValue($v);
            }

            return $value;
        }

        $key = mb_strtoupper($value);

        if (isset($this->optionsValueMap[$key])) {
            return $this->optionsValueMap[$key];
        }

        return $value;
    }
}
