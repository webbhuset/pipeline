<?php

namespace Webbhuset\Bifrost\Type;
use Webbhuset\Bifrost\BifrostException;

class UnionType extends AbstractType
{
    protected $types = [];

    protected function parseArg($arg)
    {
        if ($arg instanceof TypeInterface) {
            $this->types[] = $arg;
            return;
        }

        parent::parseArg($arg);
    }


    public function getErrors($value)
    {
        $oneTypeMatches = false;

        foreach ($this->types as $type) {
            $errors = $type->getErrors($value);
            if ($errors === false) {
                $oneTypeMatches = true;
                break;
            }
        }

        if (!$oneTypeMatches) {
            return "Value is not of any type in the union";
        }

        return false;
    }

    public function isEqual($a, $b)
    {
    }

    public function cast($value)
    {
    }
}
