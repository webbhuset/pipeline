<?php
namespace Webbhuset\Bifrost\Type\FloatType;

use Webbhuset\Bifrost\BifrostException;
use Webbhuset\Bifrost\Type;

class DecimalType extends Type\FloatType
{
    protected $tolerance = 1e-4;
    protected $decimalCount = 4;

    public function getErrors($value)
    {
        if ($error = parent::getErrors($value)){
            return $error;
        }

        if (is_null($value)) {
            return false;
        }

        $rounded = round($value, $this->decimalCount);

        if (abs($rounded - $value) > 1e-9) {
            return "To many decimals, max {$this->decimalCount} allowed: {$value}";
        }
        return false;
    }
}
