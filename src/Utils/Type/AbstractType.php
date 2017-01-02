<?php
namespace Webbhuset\Bifrost\Core\Utils\Type;
use Webbhuset\Bifrost\Core\BifrostException;

abstract class AbstractType implements TypeInterface
{
    protected $required = false;

    public function __construct($params = null)
    {
        if (isset($params['required'])) {
            if (!is_bool($params['required'])) {
                throw new BifrostException("'required' parameter must be boolean.");
            }
            $this->required = $params['required'];
        }
    }

    public function getErrors($value)
    {
        if (is_null($value) && $this->required) {
            return "Value is required";
        }

        return false;
    }

    abstract public function isEqual($a, $b);

    /**
     * Returns the string representation of a value.
     *
     * @param mixed $value
     *
     * @return string
     */
    protected function getValueString($value)
    {
        if (is_object($value)) {
            return 'Object';
        }
        if (is_array($value)) {
            return 'Array';
        }

        return (string) $value;
    }

    public function diff($old, $new)
    {
        throw new BifrostException("Diff method not implemented for this type.");
    }
}
