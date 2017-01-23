<?php

namespace Webbhuset\Bifrost\Core\Test\UnitTest\Component\Transform;

class ReduceTest
{
    public static function __constructTest($test)
    {
        $test
            ->testThatArgs(123, [])->throws('Webbhuset\\Bifrost\\Core\\BifrostException')
            ->testThatArgs(__METHOD__, [])->throws('Webbhuset\\Bifrost\\Core\\BifrostException')
            ->testThatArgs(function($one, $two, $three) {}, [])->throws('Webbhuset\\Bifrost\\Core\\BifrostException')
            ->testThatArgs(function($one, $two) {}, [])->notThrows('Webbhuset\\Bifrost\\Core\\BifrostException')
        ;
    }

    public static function processTest($test)
    {
        $test->newInstance(function($carry, $item) {
            $carry += $item;

            return $carry;
        }, 0);

        $test->testThatArgs([1, 2, 3])
            ->returnsGenerator()
            ->returnsValue([6]);

        $test->newInstance(function($carry, $item) {
            $carry[] = $item['a'];

            return $carry;
        }, []);

        $test->testThatArgs([
                ['a' => 1],
                ['a' => 2],
                ['a' => 3],
            ])
            ->returnsGenerator()
            ->returnsValue([
                [1,2,3],
            ]);

        $test->newInstance(function($carry, $item) {
            $carry += $item;

            return $carry;
        }, 100);

        $test
            ->testThatArgs([1, 1, 1, 1, 1], false)
            ->returnsGenerator()
            ->testThatArgs([1, 1, 1, 1, 1], false)
            ->returnsGenerator()
            ->testThatArgs([1, 1, 1, 1, 1])
            ->returnsGenerator()
            ->returnsValue([115]);
    }
}
