<?php

namespace Webbhuset\Whaskell\Test\UnitTest\Iterable;

class GroupTest
{
    public static function __constructTest($test)
    {
        $test
            ->testThatArgs(1)->throws('Webbhuset\\Whaskell\\WhaskellException')
            ->testThatArgs(function(){})->throws('Webbhuset\\Whaskell\\WhaskellException')
            ->testThatArgs(function($arg, $arg2){})->throws('Webbhuset\\Whaskell\\WhaskellException')
        ;
    }

    public static function __invokeTest($test)
    {
        $test->newInstance(2)
            ->testThatArgs([1, 2, 3, 4, 5])
            ->returnsGenerator()
            ->returnsStrictValue([
                [1, 2],
                [3, 4],
                [5],
            ]);

        $test->newInstance(5)
            ->testThatArgs([1, 2, 3, 4, 5])
            ->returnsGenerator()
            ->returnsStrictValue([
                [1, 2, 3, 4, 5]
            ]);

        $test->newInstance(12)
            ->testThatArgs([1, 2, 3, 4, 5], false)
            ->returnsGenerator()
            ->returnsStrictValue([]);

        $test->newInstance(function($batch, $item, $finalize) {
                if ($finalize) {
                    return true;
                }
                $last = end($batch);

                if ($last != $item) {
                    return true;
                }
            })
            ->testThatArgs([1, 1, 1, 2, 2, 3, 4, 4, 4])
            ->returnsGenerator()
            ->returnsStrictValue([
                [1, 1, 1],
                [2, 2],
                [3],
                [4, 4, 4],
            ]);

    }
}
