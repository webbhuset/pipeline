<?php

namespace Webbhuset\Whaskell;

use Webbhuset\Whaskell\Convert;
use Webbhuset\Whaskell\Dispatch;
use Webbhuset\Whaskell\Dev;
use Webbhuset\Whaskell\Flow;
use Webbhuset\Whaskell\IO;
use Webbhuset\Whaskell\Iterable;
use Webbhuset\Whaskell\Observer;
use Webbhuset\Whaskell\Validate;

class Constructor
{
    // Convert
    public static function TableToTree(...$args)
    {
        return new Convert\TableToTree(...$args);
    }

    public static function TreeToTable(...$args)
    {
        return new Convert\TreeToTable(...$args);
    }

    // Dev
    public static function Dahbug(...$args)
    {
        return new Dev\Dahbug(...$args);
    }

    public static function Mute(...$args)
    {
        return new Dev\Mute(...$args);
    }

    // Dispatch
    public static function DispatchError(...$args)
    {
        return new Dispatch\Error(...$args);
    }

    public static function DispatchEvent(...$args)
    {
        return new Dispatch\Event(...$args);
    }

    public static function DispatchSideEffect(...$args)
    {
        return new Dispatch\SideEffect(...$args);
    }

    // Flow
    public static function Compose(...$args)
    {
        return new Flow\Compose(...$args);
    }

    public static function Defer(...$args)
    {
        return new Flow\Defer(...$args);
    }

    public static function Fork(...$args)
    {
        return new Flow\Fork(...$args);
    }

    public static function Multiplex(...$args)
    {
        return new Flow\Multiplex(...$args);
    }

    // IO
    public static function DirectoryFiles(...$args)
    {
        return new IO\Directory\AllFiles(...$args);
    }

    public static function ReadCsv(...$args)
    {
        return new IO\File\Read\Csv(...$args);
    }

    public static function ReadJson(...$args)
    {
        return new IO\File\Read\Json(...$args);
    }

    public static function ReadLine(...$args)
    {
        return new IO\File\Read\Line(...$args);
    }

    public static function ReadXml(...$args)
    {
        return new IO\File\Read\Xml(...$args);
    }

    public static function WriteCsv(...$args)
    {
        return new IO\File\Write\Csv(...$args);
    }

    public static function WriteJson(...$args)
    {
        return new IO\File\Write\Json(...$args);
    }

    public static function WriteLine(...$args)
    {
        return new IO\File\Write\Json(...$args);
    }

    public static function MoveFile(...$args)
    {
        return new IO\File\Move(...$args);
    }

    // Iterable
    public static function Expand(...$args)
    {
        return new Iterable\Expand(...$args);
    }

    public static function Filter(...$args)
    {
        return new Iterable\Filter(...$args);
    }

    public static function Group(...$args)
    {
        return new Iterable\Group(...$args);
    }

    public static function Map(...$args)
    {
        return new Iterable\Map(...$args);
    }

    public static function Merge(...$args)
    {
        return new Iterable\Merge(...$args);
    }

    public static function Reduce(...$args)
    {
        return new Iterable\Reduce(...$args);
    }

    // Observe
    public static function AppendContext(...$args)
    {
        return new Observe\AppendContext(...$args);
    }

    public static function ObserveEvent(...$args)
    {
        return new Observe\Event(...$args);
    }

    public static function ObserveSideEffect(...$args)
    {
        return new Observe\SideEffect(...$args);
    }
}
