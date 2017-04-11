<?php

namespace Webbhuset\Bifrost\Test\UnitTest\Component\Transform;

class FilterTest
{
    public static function __constructTest($test)
    {
        $test
            ->testThatArgs(123)->throws('Webbhuset\\Bifrost\\Core\\BifrostException')
            ->testThatArgs(function(){})->throws('Webbhuset\\Bifrost\\Core\\BifrostException')
            ->testThatArgs(function($item){})->notThrows('Exception')
            ->testThatArgs(function($item, $arg){})->throws('Webbhuset\\Bifrost\\Core\\BifrostException')
            ->testThatArgs(function($item, $arg = 'def'){})->notThrows('Exception')
        ;
    }

    public static function processTest($test)
    {
        $test->newInstance(function($item) {
            return $item > 10;
        });

        $test
            ->testThatArgs([1, 6, 12, 20])
            ->returnsGenerator()
            ->returnsStrictValue([12, 20]);

        $test->newInstance(function($item) {
            yield $item;
        });

        $test
            ->testThatArgs([1, 6, 12, 20])
            ->returnsGenerator()
            ->throws('Webbhuset\\Bifrost\\Core\\BifrostException');
    }
}
