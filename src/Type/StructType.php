<?php
namespace Webbhuset\Bifrost\Type;
use Webbhuset\Bifrost\BifrostException;

class StructType extends AbstractType
{
    protected $fields;

    public function __construct($params)
    {
        if (!isset($params['fields']) || !is_array($params['fields'])) {
            throw new BifrostException("Fields params must be array");
        }
        foreach ($params['fields'] as $type) {
            if (!$type instanceof TypeInterface) {
                throw new BifrostException("Field parameter values must implement TypeInterface");
            }
        }

        $this->fields = $params['fields'];
    }

    public function cast($value)
    {
        $result = [];
        foreach ($this->fields as $key => $type) {
            if (!isset($value[$key])){
                continue;
            }

            $result[$key] = $type->cast($value[$key]);
        }

        return $result;
    }

    public function getErrors($value)
    {
        if (!is_array($value)) {
            $string = $this->getValueString($value);
            return "Not a valid array: '{$string}'";
        }

        $errors     = [];
        foreach ($this->fields as $key => $type) {
            if (!isset($value[$key])) {
                $value[$key] = null;
            }

            $tmpError = $type->getErrors($value[$key]);

            if ($tmpError !== false) {
                $errors[$key] = $tmpError;
            }
        }

        if (!empty($errors)) {
            return $errors;
        }

        return false;
    }

    public function isEqual($a, $b)
    {
        foreach ($this->fields as $key => $type) {
            if (!isset($a[$key]) || !isset($b[$key])){
                throw new BifrostException("Input is missing key: " . $key);
            }

            if (!$type->isEqual($a[$key], $b[$key])) {
                return false;
            }
        }

        return true;
    }

    public function diff($old, $new) {
        $result = [];
        foreach ($this->fields as $key => $type) {
            if (!isset($old[$key]) && !isset($new[$key])) {
                continue;
            }
            if (!isset($old[$key])) {
                $old[$key] = null;
            }
            if (!isset($new[$key])) {
                 $new[$key] = null;
            }

            if (!$type->isEqual($old[$key], $new[$key])) {
                $result[$key]['+'] = $new[$key];
                $result[$key]['-'] = $old[$key];
            }
        }

        return $result;
    }
}
