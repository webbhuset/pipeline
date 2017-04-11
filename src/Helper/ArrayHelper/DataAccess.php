<?php

namespace Webbhuset\Bifrost\Helper\ArrayHelper;

use ArrayAccess;
use Webbhuset\Bifrost\BifrostException;

class DataAccess implements ArrayAccess
{
    protected $pathSeparator;
    protected $defaultValue;
    protected $forceOnEmpty;
    protected $array = [];

    public function __construct(array $array, $pathSeparator = null, $defaultValue = null, $forceOnEmpty = false)
    {
        $this->pathSeparator    = $pathSeparator;
        $this->defaultValue     = $defaultValue;
        $this->forceOnEmpty     = $forceOnEmpty;
        $this->array            = $array;
    }

    public function offsetGet($offset)
    {
        if ($this->pathSeparator !== null && is_string($offset)) {
            $offset = explode($this->pathSeparator, $offset);
        }

        if (is_array($offset)) {
            $value = $this->array;

            foreach ($offset as $node) {
                if (array_key_exists($node, $value)) {
                    $value = $value[$node];
                } else {
                    $value = $this->defaultValue;
                    break;
                }
            }
        } else {
            $value  = array_key_exists($offset, $this->array)
                    ? $this->array[$offset]
                    : $this->defaultValue;
        }

        if ($this->forceOnEmpty && empty($value)) {
            return $this->defaultValue;
        }

        return $value;
    }

    public function offsetExists($offset)
    {
        throw new BifrostException("Not implemented yet");
    }

    public function offsetSet($offset, $value)
    {
        throw new BifrostException("Not implemented yet");
    }

    public function offsetUnset($offset)
    {
        throw new BifrostException("Not implemented yet");
    }

    public function getArrayFromPrefix($prefix)
    {
        $result = [];
        $len = mb_strlen($prefix);
        foreach ($this->array as $key => $value) {
            if (mb_strpos($key, $prefix) === 0) {
                $newKey = mb_substr($key, $len);
                if (!$newKey) {
                    continue;
                }

                $result[$newKey] = $value;
            }
        }

        return $result;
    }
}
