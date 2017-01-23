<?php
namespace Webbhuset\Bifrost\Core\Type\FloatType;

use Webbhuset\Bifrost\Core\BifrostException;
use Webbhuset\Bifrost\Core\Type;

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
