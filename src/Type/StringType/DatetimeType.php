<?php
namespace Webbhuset\Bifrost\Type\StringType;

use Webbhuset\Bifrost\BifrostException;
use Webbhuset\Bifrost\Type;

class DatetimeType extends Type\StringType
{
    protected $matches = [
        '/^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}$/' => 'Date is not in format "YYYY-MM-DD HH:MM:SS',
    ];
}
