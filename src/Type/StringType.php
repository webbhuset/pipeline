<?php
namespace Webbhuset\Bifrost\Type;

use Webbhuset\Bifrost\BifrostException;
use Webbhuset\Bifrost\Type\TypeConstructor AS T;

class StringType extends AbstractType
    implements TypeInterface
{
    protected $maxLen       = -1;
    protected $minLen       = -1;
    protected $matches      = [];
    protected $notMatches   = [];

    protected function parseArg($arg)
    {
        if (is_array($arg) && isset($arg[T::ARG_KEY_MIN])) {
            $this->minLen   = is_numeric($arg[T::ARG_KEY_MIN])
                            ? (int)$arg[T::ARG_KEY_MIN]
                            : null;
            return;
        }

        if (is_array($arg) && isset($arg[T::ARG_KEY_MAX])) {
            $this->maxLen   = is_numeric($arg[T::ARG_KEY_MAX])
                            ? (int)$arg[T::ARG_KEY_MAX]
                            : null;
            return;
        }

        if (isset($arg[T::ARG_KEY_MATCH])) {
            $this->matches = is_array($arg[T::ARG_KEY_MATCH])
                            ? $arg[T::ARG_KEY_MATCH]
                            : [];
            return;
        }

        if (isset($arg[T::ARG_KEY_NOTMATCH])) {
            $this->notMatches = is_array($arg[T::ARG_KEY_NOTMATCH])
                            ? $arg[T::ARG_KEY_NOTMATCH]
                            : [];
            return;
        }

        parent::parseArg($arg);
    }

    public function getErrors($value)
    {
        if ($error = parent::getErrors($value)) {
            return $error;
        }

        if (is_null($value)) {
            return false;
        }

        if (!is_string($value)) {
            $string = $this->getValueString($value);
            return "Not a valid string: '{$string}'";
        }

        if ($this->minLen >= 0 && mb_strlen($value) < $this->minLen) {
            return "String is too short, min length allowed is {$this->minLen}: '{$value}'";
        }

        if ($this->maxLen >= 0 && mb_strlen($value) > $this->maxLen) {
            return "String is too long, max length allowed is {$this->maxLen}: '{$value}'";
        }

        foreach ($this->matches as $regex => $message) {
            if (!preg_match($regex, $value)) {
                return sprintf($message, $value);
            }
        }

        foreach ($this->notMatches as $regex => $message) {
            if (preg_match($regex, $value)) {
                return sprintf($message, $value);
            }
        }

        return false;
    }

    public function cast($value)
    {
        if (is_null($value)) {
            return $value;
        }

        return (string) $value;
    }

    public function isEqual($a, $b)
    {
        if (!(is_string($a) || is_null($a))) {
            throw new BifrostException("Not a string");
        }
        if (!(is_string($b) || is_null($b))) {
            throw new BifrostException("Not a string");
        }

        return $a===$b;
    }

    public function diff($a, $b)
    {
        if ($this->isEqual($a, $b)) {
            return [];
        } else {
            return [
                '+' => $a,
                '-' => $b,
            ];
        }
    }
}
