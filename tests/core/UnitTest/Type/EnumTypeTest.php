<?php
namespace Webbhuset\Bifrost\Test\UnitTest\Type;

use Webbhuset\Bifrost\Type\TypeConstructor AS T;

class EnumTypeTest
{
    public static function __constructTest($test)
    {
        $test->testThatArgs(['asdf'])->notThrows('Exception');

        $test->testThatArgs()
            ->throws('Webbhuset\Bifrost\BifrostException');
    }

    public static function diffTest($test)
    {
    }

    public static function isEqualTest($test)
    {
    }

    public static function getErrorsTest($test)
    {
        $test->newInstance(['one', 'two', 1, 2])
            ->testThatArgs('one')->returnsFalse()
            ->testThatArgs('three')->notReturnsFalse()
            ->testThatArgs(1)->returnsFalse()
            ->testThatArgs('1')->notReturnsFalse() ;
    }

    public static function castTest($test)
    {
    }
}
