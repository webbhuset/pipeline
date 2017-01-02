<?php
namespace Webbhuset\Bifrost\Core\Test\UnitTest\Utils\ValueConverter;

class StringToFloatTest
{
    public static function convertTest($test)
    {
        $test->newInstance()
            ->testThatArgs('123')->returnsStrictValue(123.0)
            ->testThatArgs('6.787')->returnsStrictValue(6.787)
            ->testThatArgs([])->throws('Webbhuset\Bifrost\Core\BifrostException')
            ->testThatArgs(123.5)->throws('Webbhuset\Bifrost\Core\BifrostException')
            ->testThatArgs('apan6')->throws('Webbhuset\Bifrost\Core\BifrostException');
    }
}
