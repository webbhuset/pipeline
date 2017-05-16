<?php
namespace Webbhuset\Bifrost\Type;
use Webbhuset\Bifrost\BifrostException;

class BoolType extends AbstractType
    implements TypeInterface
{

    public function getErrors($value)
    {
        if ($error = parent::getErrors($value)){
            return $error;
        }

        if (is_null($value)) {
            return false;
        }

        if (!is_bool($value)) {
            $string = $this->getValueString($value);
            return "Not a valid boolean: '{$string}'";
        }

        return false;
    }

    public function cast($value)
    {
        if (is_null($value)) {
            return $value;
        }

        return (bool) $value;
    }

    public function isEqual($a, $b)
    {
        if (!is_bool($a) || !is_bool($b)) {
            throw new BifrostException("Not a boolean");
        }

        return $a===$b;
    }

    public function diff($a, $b)
    {
        if ($this->isEqual($a, $b)) {
            return [];
        } else {
            return [
                '+' => $a,
                '-' => $b,
            ];
        }
    }
}
