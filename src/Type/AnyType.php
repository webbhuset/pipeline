<?php
namespace Webbhuset\Bifrost\Type;

use Webbhuset\Bifrost\BifrostException;

class AnyType extends AbstractType
{
    public function getErrors($value)
    {
        if ($error = parent::getErrors($value)){
            return $error;
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
