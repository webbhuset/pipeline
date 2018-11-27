<?php

namespace Webbhuset\Whaskell;

use Webbhuset\Whaskell\Flow;
use Webbhuset\Whaskell\Iterable as Iterables;

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

    public static function Factory(callable $callback)
    {
        return new Flow\Factory($callback);
    }

    public static function Fork(array $functions)
    {
        return new Flow\Fork($functions);
    }

    public static function Multiplex(callable $callback, array $functions)
    {
        return new Flow\Multiplex($callback, $functions);
    }

    // Iterable

    public static function Expand(callable $callback = null)
    {
        return new Iterable\Expand($callback);
    }

    public static function Filter(callable $callback)
    {
        return new Iterable\Filter($callback);
    }

    public static function Group(callable $callback)
    {
        return new Iterable\Group($callback);
    }

    public static function GroupCount($size)
    {
        return new Iterable\GroupCount($size);
    }

    public static function Map(callable $callback)
    {
        return new Iterable\Map($callback);
    }

    public static function Observe(callable $callback)
    {
        return new Iterable\Observe($callback);
    }

    public static function Reduce(callable $callback, $initialValue = [])
    {
        return new Iterable\Reduce($callback, $initialValue);
    }

    public static function Scan(callable $callback, $initialValue = [])
    {
        return new Iterable\Scan($callback, $initialValue);
    }

    public static function Slice($amount, $skip = 0)
    {
        return new Iterable\Slice($amount, $skip);
    }
}
