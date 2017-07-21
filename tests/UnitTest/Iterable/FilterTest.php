<?php

namespace Webbhuset\Whaskell\Test\UnitTest\Iterable;

class FilterTest
{
    public static function __constructTest($test)
    {
        $test
            ->testThatArgs(123)->throws('Webbhuset\\Whaskell\\WhaskellException')
            ->testThatArgs(function(){})->throws('Webbhuset\\Whaskell\\WhaskellException')
            ->testThatArgs(function($item){})->notThrows('Exception')
            ->testThatArgs(function($item, $arg){})->throws('Webbhuset\\Whaskell\\WhaskellException')
            ->testThatArgs(function($item, $arg = 'def'){})->notThrows('Exception')
        ;
    }

    public static function __invokeTest($test)
    {
        $test->newInstance(function($item) {
            return $item > 10;
        });

        $test
            ->testThatArgs([1, 6, 12, 20])
            ->returnsGenerator()
            ->returnsStrictValue([12, 20]);
    }
}
