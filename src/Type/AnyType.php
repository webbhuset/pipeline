<?php
namespace Webbhuset\Bifrost\Core\Type;

use Webbhuset\Bifrost\Core\BifrostException;

class AnyType extends AbstractType
{
    public function __construct($params = null)
    {
        parent::__construct($params);
    }

    public function getErrors($value)
    {
        if ($error = parent::getErrors($value)){
            return $error;
        }

        if (is_null($value)) {
            return false;
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
