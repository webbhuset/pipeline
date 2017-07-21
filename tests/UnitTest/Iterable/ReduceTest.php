<?php

namespace Webbhuset\Whaskell\Test\UnitTest\Iterable;

class ReduceTest
{
    public static function __constructTest($test)
    {
        $test
            ->testThatArgs(123, [])->throws('Webbhuset\\Whaskell\\WhaskellException')
            ->testThatArgs(__METHOD__, [])->throws('Webbhuset\\Whaskell\\WhaskellException')
            ->testThatArgs(function($one, $two, $three) {}, [])->throws('Webbhuset\\Whaskell\\WhaskellException')
            ->testThatArgs(function($one, $two) {}, [])->notThrows('Webbhuset\\Whaskell\\WhaskellException')
        ;
    }

    public static function __invokeTest($test)
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
