<?php
namespace Webbhuset\Bifrost\Core\Utils\Type;

interface TypeInterface
{
    public function __construct($params);
    public function cast($value);
    public function getErrors($value);
    public function isEqual($a, $b);
}
