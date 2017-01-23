<?php

namespace Webbhuset\Bifrost\Core\Helper\ArrayHelper;

use Webbhuset\Bifrost\Core\Type;

class KeyMapper
{
    protected $map;
    protected $children;
    protected $wildcard;
    protected $default;

    public function __construct(
        array $map,
        $default    = null,
        $children   = [],
        $wildcard   = '*'
    ) {
        $mapType = new Type\HashmapType([
            'key_type'      => new Type\ScalarType(),
            'value_type'    => new Type\ScalarType(),
        ]);

        $errors = $mapType->getErrors($map);

        if ($errors) {
            throw new Type\TypeException("Constructor param map is not key values.", null, null, $errors);
        }

        $this->map          = $map;
        $this->children     = $children;
        $this->wildcard     = $wildcard;
        $this->default      = $default;
    }

    public function map($array, $filter = false, $overwrite = false)
    {
        if (!is_array($array)) {
            if ($this->default === null) {
                return $array;
            }

            return [$this->default => $array];
        }

        $newArray = [];

        foreach ($array as $key => $value) {
            if (array_key_exists($key, $this->children)) {
                $value = $this->children[$key]->map($value);
            } elseif (array_key_exists($this->wildcard, $this->children)) {
                $value = $this->children[$this->wildcard]->map($value);
            }

            if (!array_key_exists($key, $this->map)) {
                $array[$key] = $value;
                continue;
            }


            $newKey             = $this->map[$key];
            $newArray[$newKey]  = $value;
            unset($array[$key]);
        }

        if ($filter) {
            return $newArray;
        }

        if ($overwrite) {
            return array_replace($array, $newArray);
        } else {
            return array_replace($newArray, $array);
        }
    }

    public function flip()
    {
        $children = [];
        $default = null;

        if ($this->default !== null && array_key_exists($this->default, $this->map)) {
            $default = $this->map[$this->default];
        }

        foreach ($this->children as $key => $child) {
            if (array_key_exists($key, $this->map)) {
                $key = $this->map[$key];
            }
            $children[$key] = $child->flip();
        }

        return new self(array_flip($this->map), $default, $children, $this->wildcard);
    }

    public function addChildren($children, $wildcard = '*')
    {
        return new self($this->map, $this->default, $children, $wildcard);
    }
}
