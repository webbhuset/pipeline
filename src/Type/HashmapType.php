<?php
namespace Webbhuset\Bifrost\Type;
use Webbhuset\Bifrost\BifrostException;

class HashmapType extends AbstractType
    implements TypeInterface
{
    protected $valueType;
    protected $keyType;
    protected $max;
    protected $min;

    public function __construct($params)
    {
        if (!isset($params['value_type'])) {
            throw new BifrostException("Value type parameter not set.");
        }
        if (!$params['value_type'] instanceof TypeInterface) {
            throw new BifrostException("Value type param must implement TypeInterface");
        }

        if (!isset($params['key_type'])) {
            throw new BifrostException("Key type parameter not set.");
        }
        if (!$params['key_type'] instanceof TypeInterface) {
            throw new BifrostException("Key type param must implement TypeInterface");
        }
        $this->valueType = $params['value_type'];
        $this->keyType   = $params['key_type'];

        if (isset($params['max_size'])) {
            if (!is_numeric($params['max_size'])) {
                throw new BifrostException("Max size must be numeric");
            }
            $this->max = $params['max_size'];
        }
        if (isset($params['min_size'])) {
            if (!is_numeric($params['min_size'])) {
                throw new BifrostException("Min size must be numeric");
            }
            $this->min = $params['min_size'];
        }
    }

    public function cast($dataArray)
    {
        if (!is_array($dataArray)) {
            throw new BifrostException("Can only cast arrays.");
        }

        $result = [];
        foreach ($dataArray as $dataKey => $dataValue) {
            $castedKey          = $this->keyType->cast($dataKey);
            $result[$castedKey] = $this->valueType->cast($dataValue);
        }

        return $result;
    }

    public function getErrors($dataArray)
    {
        if ($error = parent::getErrors($dataArray)) {
            return $error;
        }

        if (is_null($dataArray)) {
            return false;
        }

        if (!is_array($dataArray)) {
            $string = $this->getValueString($dataArray);
            return "Not a valid array: '{$string}'";
        }

        $size = count($dataArray);
        if (isset($this->min) && $size < $this->min) {
            return "Set size is too small, min size allowed is {$this->min}: '{$size}'";
        }

        if (isset($this->max) && $size > $this->max) {
            return "Set size is too big, max size allowed is {$this->max}: '{$size}'";
        }

        $errors     = [];
        foreach ($dataArray as $dataKey => $dataValue) {
            $tmpError = $this->keyType->getErrors($dataKey);
            if ($tmpError !== false) {
                $errors[] = 'Hashmap key error: ' . $tmpError;
            }

            $tmpError = $this->valueType->getErrors($dataValue);
            if ($tmpError !== false) {
                if (is_array($tmpError)) {
                    $tmpError = implode(', ', $tmpError);
                }
                $errors[] = 'Hashmap value error: ' . $tmpError;
            }
        }

        if (!empty($errors)) {
            return $errors;
        }

        return false;
    }

    public function isEqual($a, $b)
    {
        $diff = array_diff_key($a, $b);
        if (!empty($diff)) {
            return false;
        }
        $diff = array_diff_key($b, $a);
        if (!empty($diff)) {
            return false;
        }

        foreach ($a as $key => $aValue) {
            if (!$this->valueType->isEqual($aValue, $b[$key])) {
                return false;
            }
        }

        return true;
    }

    public function diff($new, $old) {
        $result = [];

        $keyDiff = array_diff_key($new, $old);
        if (!empty($keyDiff)) {
            $result['+'] = $keyDiff;
        }

        $keyDiff = array_diff_key($old, $new);
        if (!empty($keyDiff)) {
            $result['-'] = $keyDiff;
        }

        $keyIntersect = array_intersect_key($new, $old);
        foreach ($keyIntersect as $key => $newValue) {
            $diff = $this->valueType->diff($new[$key], $old[$key]);
            if (isset($diff['+'])) {
                $result['+'][$key] = $diff['+'];
            }
            if (isset($diff['-'])) {
                $result['-'][$key] = $diff['-'];
            }
        }

        return $result;
    }
}
