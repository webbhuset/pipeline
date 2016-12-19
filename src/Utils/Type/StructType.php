<?php
namespace Webbhuset\Bifrost\Core\Utils\Type;
use Webbhuset\Bifrost\Core\BifrostException;

class StructType extends AbstractType
    implements TypeInterface
{
    protected $fields;

    public function __construct($params)
    {
        if (!is_array($params)) {
            throw new BifrostException("Params must be array");
        }
        foreach ($params as $type) {
            if (!$type instanceof TypeInterface) {
                throw new BifrostException("Params values must implement TypeInterface");
            }
        }

        $this->fields = $params;
    }

    public function cast($value)
    {
        $result = [];
        foreach ($this->fields as $key => $type) {
            if (!isset($value[$key])){
                throw new BifrostException("Input is missing key: " . $key);
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
            if (!isset($value[$key])){
                $tmpError = "Input is missing key: " . $key;
            } else {
                $tmpError = $type->getErrors($value[$key]);
            }

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
        $result = [
            '+' => [],
            '-' => [],
        ];
        foreach ($this->fields as $key => $type) {
            if (!isset($old[$key]) || !isset($new[$key])){
                throw new BifrostException("Input is missing key: " . $key);
            }

            if (!$type->isEqual($old[$key], $new[$key])) {
                $result['+'][$key] = $new[$key];
                $result['-'][$key] = $old[$key];
            }
        }

        return $result;
    }
}
