<?php

namespace Webbhuset\Bifrost\Type;
use Webbhuset\Bifrost\BifrostException;

class UnionType extends AbstractType
{
    protected $types;

    public function __construct($params)
    {
        parent::__construct($params);
        if (!isset($params['types']) || !is_array($params['types'])) {
            throw new BifrostException("You must specify types.");
        }

        foreach ($params['types'] as $type) {
            if (!$type instanceof TypeInterface) {
                throw new BifrostException("Type parameter values must implement TypeInterface");
            }
        }

        $this->types = $params['types'];
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
