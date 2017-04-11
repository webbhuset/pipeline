<?php
namespace Webbhuset\Bifrost\Utils\ValueConverter;
use \Webbhuset\Bifrost\BifrostException;

class StringToInt implements ValueConverterInterface
{
    public function convert($value)
    {
        if (!is_string($value)) {
            throw new BifrostException('Value must be a string');
        }
        if (!is_numeric($value)) {
            throw new BifrostException('Value must be numeric');
        }
        $intValue = (int) $value;
        if ($value != $intValue) {
            throw new BifrostException('Value is probably a float.');
        }

        return $intValue;
    }
}
