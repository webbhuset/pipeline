<?php
namespace Webbhuset\Bifrost\Core\Utils\Type;

class FloatType extends TypeAbstract
    implements TypeInterface
{
    protected $max;
    protected $min;
    protected $tolerance = 1e-5;

    public function __construct($params = null)
    {
        parent::__construct($params);
        if (isset($params['max_value'])) {
            $this->max = $params['max_value'];
        }
        if (isset($params['min_value'])) {
            $this->min = $params['min_value'];
        }
        if (isset($params['tolerance'])) {
            $this->tolerance = $params['tolerance'];
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

        if (!is_float($value)) {
            $string = $this->_getValueString($value);
            return "Not a valid float: '{$string}'";
        }

        if (isset($this->min) && $value <= $this->min) {
            return "Float value is too small, min value allowed is {$this->min}: '{$value}'";
        }

        if (isset($this->max) && $value >= $this->max) {
            return "Float value is too big, max value allowed is {$this->max}: '{$value}'";
        }

        return false;
    }

    public function isEqual($a, $b)
    {
        if (!is_float($a) || !is_float($b)) {
            throw new \Webbhuset\Bifrost\Core\BifrostException("Not a float");
        }

        return abs($a - $b) < $this->tolerance;
    }

    public function cast($value)
    {
        if (is_null($value)) {
            return $value;
        }

        return (float) $value;
    }
}
