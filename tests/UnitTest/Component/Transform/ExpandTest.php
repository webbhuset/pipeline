<?php

namespace Webbhuset\Bifrost\Test\UnitTest\Component\Transform;

class ExpandTest
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
            return [];
        });

        $test->testThatArgs(['a'])
            ->returnsGenerator()
            ->throws('Webbhuset\\Bifrost\\Core\\BifrostException');

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
