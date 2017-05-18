<?php
namespace Webbhuset\Bifrost\Type;

use Webbhuset\Bifrost\Type\TypeConstructor AS T;
use Webbhuset\Bifrost\BifrostException;

abstract class AbstractType implements TypeInterface
{
    protected $isNullable = false;

    public function __construct()
    {
        $args = func_get_args();

        foreach ($args as $arg) {
            $this->parseArg($arg);
        }

        $this->afterConstruct();
    }

    protected function afterConstruct()
    {

    }

    protected function parseArg($arg)
    {
        if ($arg == T::NULLABLE) {
            $this->isNullable = true;
        }
    }

    public function getErrors($value)
    {
        if (is_null($value) && !$this->isNullable) {
            return "Value is required";
        }

        return false;
    }

    public function isEqual($a, $b)
    {
        return $a == $b;
    }

    public function cast($value)
    {
        return $value;
    }

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

    public function diff($a, $b)
    {
        throw new BifrostException("Diff method not implemented for this type.");
    }
}
