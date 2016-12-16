<?php
namespace Webbhuset\Bifrost\Core\Utils\ValueConverter;

interface ValueConverter
{
    public function __construct($params);
    public function convert($value);
}
