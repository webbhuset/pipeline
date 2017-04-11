<?php
namespace Webbhuset\Bifrost\Test\UnitTest\Utils\ValueConverter;

class StringToIntTest
{
    public static function convertTest($test)
    {
        $test->newInstance()
            ->testThatArgs('123')->returnsStrictValue(123)
            ->testThatArgs(123)->throws('Webbhuset\Bifrost\BifrostException')
            ->testThatArgs(123.5)->throws('Webbhuset\Bifrost\BifrostException')
            ->testThatArgs('123.6')->throws('Webbhuset\Bifrost\BifrostException');
    }
}
