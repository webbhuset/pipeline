<?php
namespace Webbhuset\Bifrost\Core\Utils\ValueConverter;
use \Webbhuset\Bifrost\Core\BifrostException;

class StringToFloat implements ValueConverterInterface
{
    public function convert($value)
    {
        if (!is_string($value)) {
            throw new BifrostException('Value must be a string');
        }
        if (!is_numeric($value)) {
            throw new BifrostException('Value must be numeric');
        }

        return (float) $value;
    }
}
