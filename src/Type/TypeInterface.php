<?php
namespace Webbhuset\Bifrost\Type;

interface TypeInterface
{
    public function __construct($params);
    public function cast($value);
    public function getErrors($value);
    public function isEqual($a, $b);
    public function diff($old, $new);
}
