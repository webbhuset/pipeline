<?php
namespace Webbhuset\Bifrost\Core\Utils\Type;
use Webbhuset\Bifrost\Core\BifrostException;

class IntType extends AbstractType
    implements TypeInterface
{
    protected $max;
    protected $min;

    public function __construct($params = null)
    {
        parent::__construct($params);
        if (isset($params['max_value'])) {
            if (!is_numeric($params['max_value'])) {
                throw new BifrostException("Max value must be numeric");
            }
            $this->max = $params['max_value'];
        }

        if (isset($params['min_value'])) {
            if (!is_numeric($params['min_value'])) {
                throw new BifrostException("Min value must be numeric");
            }
            $this->min = $params['min_value'];
        }
    }

    public function getErrors($value)
    {
        if ($error = parent::getErrors($value)){
            return $error;
        }

        if (is_null($value)) {
            return false;
        }

        if (!is_integer($value)) {
            $string = $this->getValueString($value);
            return "Not a valid integer: '{$string}'";
        }

        if (isset($this->min) && $value <= $this->min) {
            return "Integer value is too small, min value allowed is {$this->min}: '{$value}'";
        }

        if (isset($this->max) && $value >= $this->max) {
            return "Integer value is too big, max value allowed is {$this->max}: '{$value}'";
        }

        return false;
    }

    public function cast($value)
    {
        if (is_null($value)) {
            return $value;
        }

        return (int) $value;
    }

    public function isEqual($a, $b)
    {
        if (!is_int($a) || !is_int($b)) {
            throw new BifrostException("Not a integer");
        }

        return $a===$b;
    }
}
