<?php

namespace Webbhuset\Whaskell\Test\UnitTest\Iterable;

class ExpandTest
{
    public static function __constructTest($test)
    {
        $test
            ->testThatArgs(123)->throws('Webbhuset\\Whaskell\\WhaskellException')
            ->testThatArgs(function(){yield 1;})->throws('Webbhuset\\Whaskell\\WhaskellException')
            ->testThatArgs(function(){})->throws('Webbhuset\\Whaskell\\WhaskellException')
            ->testThatArgs(function($item){yield 1;})->notThrows('Exception')
            ->testThatArgs(function($item, $arg){yield 1;})->throws('Webbhuset\\Whaskell\\WhaskellException')
            ->testThatArgs(function($item, $arg = 'def'){yield 1;})->notThrows('Exception')
        ;
    }

    public static function __invokeTest($test)
    {
        $test->newInstance(function($skus) {
            foreach ($skus as $sku) {
                yield ['sku' => $sku];
            }
        });

        $test->testThatArgs([['a', 'b', 'c']])
            ->returnsGenerator()
            ->returnsStrictValue([
                ['sku' => 'a'],
                ['sku' => 'b'],
                ['sku' => 'c'],
            ]);

        $test->newInstance(function($max) {
            for ($i = 0; $i < $max; $i++) {
                yield $i;
            }
        });

        $test->testThatArgs([2, 3])
            ->returnsGenerator()
            ->returnsStrictValue([
                0,
                1,
                0,
                1,
                2,
            ]);
    }
}
