<?php
namespace Webbhuset\Bifrost\Core\Test\UnitTest\Type\StringType;

class DateTimeTypeTest
{
    public static function __constructTest($test)
    {
    }

    public static function isEqualTest($test)
    {
    }

    public static function getErrorsTest($test)
    {
        $test->newInstance()
            ->testThatArgs('asdf')->returnsString()
            ->testThatArgs('2000-10-01 00:00:00')->returnsFalse()
            ->testThatArgs('2000-10-01')->returnsString()
        ;
    }

    public static function castTest($test)
    {
    }

    public static function diffTest($test)
    {
    }
}
