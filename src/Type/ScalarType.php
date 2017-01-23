<?php

namespace Webbhuset\Bifrost\Core\Type;

use Webbhuset\Bifrost\Core\BifrostException;

class ScalarType extends AbstractType
{
    public function __construct($params = null)
    {

    }

    public function getErrors($value)
    {
        if ($error = parent::getErrors($value)){
            return $error;
        }

        if (is_null($value)) {
            return false;
        }

        if (!is_scalar($value)) {
            $string = $this->getValueString($value);
            return "Not a valid scalar value: '{$string}'";
        }

        return false;
    }

    public function cast($value)
    {

    }

    public function isEqual($a, $b)
    {

    }
}
