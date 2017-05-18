<?php
namespace Webbhuset\Bifrost\Type;

use Webbhuset\Bifrost\Type\TypeConstructor AS T;
use Webbhuset\Bifrost\BifrostException;

class HashmapType extends AbstractType
    implements TypeInterface
{
    protected $valueType;
    protected $keyType;
    protected $max;
    protected $min;

    protected function parseArg($arg)
    {
        if (is_array($arg) && isset($arg[T::ARG_KEY_MIN])) {
            $this->min  = is_int($arg[T::ARG_KEY_MIN])
                        ? $arg[T::ARG_KEY_MIN]
                        : null;
            return;
        }

        if (is_array($arg) && isset($arg[T::ARG_KEY_MAX])) {
            $this->max  = is_int($arg[T::ARG_KEY_MAX])
                        ? $arg[T::ARG_KEY_MAX]
                        : null;
            return;
        }

        if ($arg instanceof TypeInterface && empty($this->keyType)) {
            $this->keyType = $arg;
            return;
        }

        if ($arg instanceof TypeInterface && empty($this->valueType)) {
            $this->valueType = $arg;
            return;
        }

        parent::parseArg($arg);
    }

    protected function afterConstruct()
    {
        if (!$this->keyType instanceof TypeInterface) {
            throw new BifrostException("Key type param must implement TypeInterface");
        }

        if (!$this->valueType instanceof TypeInterface) {
            throw new BifrostException("Value type param must implement TypeInterface");
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

        if (!isset($result['+'])) {
            $result['+'] = [];
        }

        if (!isset($result['-'])) {
            $result['-'] = [];
        }

        return $result;
    }
}
