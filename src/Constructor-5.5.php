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
use ReflectionClass;

class Constructor
{
    // Convert
    public static function TreeToLeaves()
    {
        $reflection = new ReflectionClass(Convert\TreeToLeaves::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    public static function RowsToTree()
    {
        $reflection = new ReflectionClass(Convert\RowsToTree::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    public static function TreeToRows()
    {
        $reflection = new ReflectionClass(Convert\TreeToRows::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    // Dev
    public static function Dahbug()
    {
        $reflection = new ReflectionClass(Dev\Dahbug::class);
        $args       = func_get_args();
        array_unshift($args, debug_backtrace());

        return $reflection->newInstanceArgs($args);
    }

    public static function DahbugWrite()
    {
        $reflection = new ReflectionClass(Dev\DahbugWrite::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    public static function Mute()
    {
        $reflection = new ReflectionClass(Dev\Mute::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    // Dispatch
    public static function DispatchError()
    {
        $reflection = new ReflectionClass(Dispatch\Error::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    public static function DispatchEvent()
    {
        $reflection = new ReflectionClass(Dispatch\Event::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    public static function DispatchSideEffect()
    {
        $reflection = new ReflectionClass(Dispatch\SideEffect::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    // Flow
    public static function Compose()
    {
        $reflection = new ReflectionClass(Flow\Compose::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    public static function Defer()
    {
        $reflection = new ReflectionClass(Flow\Defer::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    public static function Factory()
    {
        $reflection = new ReflectionClass(Flow\Factory::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    public static function Fork()
    {
        $reflection = new ReflectionClass(Flow\Fork::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    public static function Multiplex()
    {
        $reflection = new ReflectionClass(Flow\Multiplex::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    // IO
    public static function DirectoryFiles()
    {
        $reflection = new ReflectionClass(IO\Directory\AllFiles::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    public static function ReadCsv()
    {
        $reflection = new ReflectionClass(IO\File\Read\Csv::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    public static function ReadJson()
    {
        $reflection = new ReflectionClass(IO\File\Read\Json::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    public static function ReadJsonDecode()
    {
        $reflection = new ReflectionClass(IO\File\Read\JsonDecode::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    public static function ReadLine()
    {
        $reflection = new ReflectionClass(IO\File\Read\Line::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    public static function ReadRaw()
    {
        $reflection = new ReflectionClass(IO\File\Read\Raw::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    public static function ReadXml()
    {
        $reflection = new ReflectionClass(IO\File\Read\Xml::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    public static function WriteCsv()
    {
        $reflection = new ReflectionClass(IO\File\Write\Csv::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    public static function WriteJson()
    {
        $reflection = new ReflectionClass(IO\File\Write\Json::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    public static function WriteLine()
    {
        $reflection = new ReflectionClass(IO\File\Write\Line::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    public static function MoveFile()
    {
        $reflection = new ReflectionClass(IO\File\Move::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    // Iterable
    public static function ApplyCombine()
    {
        $reflection = new ReflectionClass(Iterable\ApplyCombine::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    public static function Expand()
    {
        $reflection = new ReflectionClass(Iterable\Expand::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    public static function Filter()
    {
        $reflection = new ReflectionClass(Iterable\Filter::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    public static function Group()
    {
        $reflection = new ReflectionClass(Iterable\Group::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    public static function Map()
    {
        $reflection = new ReflectionClass(Iterable\Map::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    public static function Merge()
    {
        $reflection = new ReflectionClass(Iterable\Merge::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    public static function Reduce()
    {
        $reflection = new ReflectionClass(Iterable\Reduce::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    // Observe
    public static function AppendContext()
    {
        $reflection = new ReflectionClass(Observe\AppendContext::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    public static function ObserveEvent()
    {
        $reflection = new ReflectionClass(Observe\Event::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    public static function ObserveSideEffect()
    {
        $reflection = new ReflectionClass(Observe\SideEffect::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }

    public static function ObserveException()
    {
        $reflection = new ReflectionClass(Observe\Exception::class);
        $args       = func_get_args();

        return $reflection->newInstanceArgs($args);
    }
}
