<?php
namespace Webbhuset\Bifrost\Type;

use Webbhuset\Bifrost\Type\TypeConstructor AS T;
use Webbhuset\Bifrost\BifrostException;


class SetType extends AbstractType
    implements TypeInterface
{
    protected $max;
    protected $min;
    protected $type;

    protected function parseArg($arg)
    {
        if (is_array($arg) && isset($arg[T::ARG_KEY_MIN])) {
            $this->min  = is_int($arg[T::ARG_KEY_MIN])
                        ? $arg[T::ARG_KEY_MIN]
                        : null;
            return;
        }

        if (is_array($arg) && isset($arg[T::ARG_KEY_MAX])) {
            $this->max  = is_int($arg[T::ARG_KEY_MAX])
                        ? $arg[T::ARG_KEY_MAX]
                        : null;
            return;
        }

        if ($arg instanceof TypeInterface) {
            $this->type = $arg;
            return;
        }

        parent::parseArg($arg);
    }

    protected function afterConstruct()
    {
        if (!$this->type instanceof TypeInterface) {
            throw new BifrostException("Type param is missing for set.");
        }

    }

    public function cast($valueArray)
    {
        if (!is_array($valueArray)) {
            return $this->type->cast($valueArray);
        }

        $result = [];
        foreach ($valueArray as $value) {
            $result[] = $this->type->cast($value);
        }

        return $result;
    }

    public function getErrors($valueArray)
    {
        if ($error = parent::getErrors($valueArray)){
            return $error;
        }
        if (!is_array($valueArray)) {
            $string = $this->getValueString($valueArray);
            return "Not a valid array: '{$string}'";
        }

        $size = count($valueArray);
        if ($size !== count(array_unique($valueArray))) {
            $string = $this->getValueString($valueArray);
            return "Values must be unique";
        }

        if (isset($this->min) && $size < $this->min) {
            return "Set size is too small, min size allowed is {$this->min}: '{$size}'";
        }

        if (isset($this->max) && $size > $this->max) {
            return "Set size is too big, max size allowed is {$this->max}: '{$size}'";
        }

        $errors     = [];
        foreach ($valueArray as $value) {
            $tmpError = $this->type->getErrors($value);

            if ($tmpError !== false) {
                $errors[] = $tmpError;
            }
        }

        if (!empty($errors)) {
            return $errors;
        }

        return false;
    }

    public function isEqual($a, $b)
    {
        if (!is_array($a) || !is_array($b)) {
            throw new BifrostException("Values must be arrays to be compared.");
        }

        if (count($a) != count($b)) {
            return false;
        }

        sort($a);
        sort($b);

        for ($i=0; $i < count($a); $i++) {
            if (!$this->type->isEqual($a[$i], $b[$i])) {
                return false;
            }
        }

        return true;
    }

    public function diff($new, $old)
    {
        if (!is_array($old) || !is_array($new)) {
            throw new BifrostException("Values must be arrays to be compared.");
        }

        $result = [
            '+' => [],
            '-' => [],
        ];

        foreach ($old as $element) {
            $key = array_search($element, $new);
            if ($key === false) {
                $result['-'][] = $element;
                continue;
            }
            if (!$this->type->isEqual($element, $new[$key])) {
                $result['+'][] = $new[$key];
                $result['-'][] = $element;
            }
        }

        foreach ($new as $element) {
            if (array_search($element, $old) === false) {
                $result['+'][] = $element;
            }
        }

        return $result;
    }
}
