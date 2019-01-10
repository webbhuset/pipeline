<?php

namespace Webbhuset\Pipeline;

use Webbhuset\Pipeline\Flow;
use Webbhuset\Pipeline\Value;

class Constructor
{
    // Flow

    public static function Compose(array $functions)
    {
        return new Flow\Compose($functions);
    }

    public static function Defer(callable $callback)
    {
        return new Flow\Defer($callback);
    }

    public static function Fork(array $functions)
    {
        return new Flow\Fork($functions);
    }

    public static function Multiplex(callable $callback, array $functions)
    {
        return new Flow\Multiplex($callback, $functions);
    }

    // Value

    public static function Chunk($size)
    {
        return new Value\Chunk($size);
    }

    public static function Drop($amount)
    {
        return new Value\Drop($amount);
    }

    public static function DropWhile(callable $callback)
    {
        return new Value\DropWhile($callback);
    }

    public static function Expand(callable $callback = null)
    {
        return new Value\Expand($callback);
    }

    public static function Filter(callable $callback = null)
    {
        return new Value\Filter($callback);
    }

    public static function Group(callable $callback)
    {
        return new Value\Group($callback);
    }

    public static function Map(callable $callback)
    {
        return new Value\Map($callback);
    }

    public static function Observe(callable $callback)
    {
        return new Value\Observe($callback);
    }

    public static function Reduce(callable $callback, $initialValue = [])
    {
        return new Value\Reduce($callback, $initialValue);
    }

    public static function Scan(callable $callback, $initialValue = [])
    {
        return new Value\Scan($callback, $initialValue);
    }

    public static function Take($amount)
    {
        return new Value\Take($amount);
    }

    public static function TakeWhile(callable $callback)
    {
        return new Value\TakeWhile($callback);
    }
}
