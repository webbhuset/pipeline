<?php

namespace Webbhuset\Bifrost\Core\Test\UnitTest\Type;

class TypeConstructorTest
{
    public static function __callStaticTest($test)
    {
        $ns = 'Webbhuset\\Bifrost\\Core\\Type\\';
        $test
            ->testThatArgs('StringDatetime', [])->returnsInstanceOf($ns.'StringType\\DatetimeType')
            ->testThatArgs('fisk', [])->throws($ns.'TypeException')
        ;
    }

    public static function getClassNameTest($test)
    {
        $test
            ->testThatArgs('StrDatetime')
            ->testThatArgs('String')
            ->testThatArgs('string')
            ->testThatArgs('Map')
            ;
    }
}
