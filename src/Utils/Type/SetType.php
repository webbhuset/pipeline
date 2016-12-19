<?php
namespace Webbhuset\Bifrost\Core\Utils\Type;
use Webbhuset\Bifrost\Core\BifrostException;

class SetType extends AbstractType
    implements TypeInterface
{
    protected $max;
    protected $min;
    protected $type;

    public function __construct($params)
    {
        if (!isset($params['type'])) {
            throw new BifrostException("Type parameter not set.");
        }
        if (!$params['type'] instanceof TypeInterface) {
            throw new BifrostException("Type param must implement TypeInterface");
        }
        $this->type = $params['type'];

        if (isset($params['max_size'])) {
            if (!is_numeric($params['max_size'])) {
                throw new BifrostException("Max size must be numeric");
            }
            $this->max = $params['max_size'];
        }
        if (isset($params['min_size'])) {
            if (!is_numeric($params['min_size'])) {
                throw new BifrostException("Min size must be numeric");
            }
            $this->min = $params['min_size'];
        }
    }

    public function cast($valueArray)
    {
        if (!is_array($valueArray)) {
            throw new BifrostException("Can only cast arrays.");
        }

        $result = [];
        foreach ($valueArray as $value) {
            $result[] = $this->type->cast($value);
        }

        return $result;
    }

    public function getErrors($valueArray)
    {
        if (!is_array($valueArray)) {
            $string = $this->_getValueString($valueArray);
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

    public function diff($old, $new) {

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
