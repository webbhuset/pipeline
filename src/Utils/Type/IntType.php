<?php
namespace Webbhuset\Bifrost\Core\Utils\Type;

class IntType extends TypeAbstract
    implements TypeInterface
{
    protected $max;
    protected $min;

    public function __construct($params = null)
    {
        parent::__construct($params);
        if (isset($params['max_value'])) {
            $this->max = $params['max_value'];
        }
        if (isset($params['min_value'])) {
            $this->min = $params['min_value'];
        }
    }

    public function sanitize($value)
    {
        if (is_null($value)) {
            return null;
        }

        if (is_string($value)) {
            $value = str_replace(',', '.', $value);
            $value = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
        }

        return (int) $value;
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
            $string = $this->_getValueString($value);
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

}
