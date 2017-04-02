<?php
namespace Webbhuset\Bifrost\Core\Type;
use Webbhuset\Bifrost\Core\BifrostException;

class StringType extends AbstractType
    implements TypeInterface
{
    protected $maxLen       = -1;
    protected $minLen       = -1;
    protected $matches      = [];
    protected $notMatches   = [];

    public function __construct($params = null)
    {
        parent::__construct($params);
        if (isset($params['max_length'])) {
            if (!is_numeric($params['max_length'])) {
                throw new BifrostException("Max length must be numeric");
            }
            $this->maxLen = $params['max_length'];
        }
        if (isset($params['min_length'])) {
            if (!is_numeric($params['min_length'])) {
                throw new BifrostException("Min length must be numeric");
            }
            $this->minLen = $params['min_length'];
        }
        if (isset($params['min_length'])) {
            if (!is_numeric($params['min_length'])) {
                throw new BifrostException("Min length must be numeric");
            }
            $this->minLen = $params['min_length'];
        }
        if (isset($params['matches'])) {
            if (!is_array($params['matches'])) {
                throw new BifrostException("Matches should be in the form <regex> => <message>");
            }
            $this->matches = $params['matches'];
        }
        if (isset($params['not_matches'])) {
            if (!is_array($params['not_matches'])) {
                throw new BifrostException("Matches should be in the form <regex> => <message>");
            }
            $this->notMatches = $params['not_matches'];
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
}
