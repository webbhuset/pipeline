<?php
namespace Webbhuset\Bifrost\Type;

use Webbhuset\Bifrost\Type\TypeConstructor AS T;
use Webbhuset\Bifrost\BifrostException;


class EnumType extends AbstractType
    implements TypeInterface
{
    protected $values = [];

    protected function parseArg($arg)
    {
        if (is_array($arg)) {
            $this->values = $arg;
            return;
        }

        parent::parseArg($arg);
    }

    protected function afterConstruct()
    {
        if (empty($this->values)) {
            throw new BifrostException("No values in enumerated type.");
        }
    }

    public function getErrors($value)
    {
        if ($error = parent::getErrors($value)){
            return $error;
        }

        if (!in_array($value, $this->values, true)) {
            $string = $this->getValueString($value);

            return "Value '{$string}' is not among the enumerated values";
        }

        return false;
    }
}
