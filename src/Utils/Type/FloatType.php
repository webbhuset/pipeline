<?php
namespace Webbhuset\Bifrost\Core\Utils\Type;

class FloatType extends TypeAbstract
    implements TypeInterface
{
    protected $max;
    protected $min;
    protected $precision;
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
        if (isset($params['precision'])) {
            $this->precision = $params['precision'];
        }
        if (isset($params['tolerance'])) {
            $this->tolerance = $params['tolerance'];
        }
    }

    public function sanitize($value)
    {
        if (is_null($value)) {
            return null;
        }

        if (is_string($value)) {
            $value = str_replace(',', '.', $value);
            $value = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        }

        if (isset($this->precision)) {
            $value = (float) $value;
            $value = round($value, $this->precision);
        }

        return (float) $value;
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
        if (gettype($a)!=='double' || gettype($b)!=='double') {
            return false;
        }

        return abs($a - $b) < $this->tolerance;
    }
}
