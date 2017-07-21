<?php

namespace Webbhuset\Whaskell\Test\UnitTest\Iterable;

class MapTest
{
    public static function __constructTest($test)
    {
        $e = 'Webbhuset\\Whaskell\\WhaskellException';
        $test
            ->testThatArgs(123)->throws($e)
            ->testThatArgs(function(){})->throws($e)
            ->testThatArgs(function($item){})->notThrows('Exception')
            ->testThatArgs(function($item, $arg){})->throws($e)
            ->testThatArgs(function($item, $arg = 'def'){})->notThrows('Exception')
        ;
    }

    public static function __invokeTest($test)
    {
        $test->newInstance(function($item) {
            return [
                'b'  => $item['a'],
                'bb' => $item['aa'],
            ];
        });

        $test->testThatArgs([
                [
                    'a' => 1,
                    'aa' => 2,
                ],
                [
                    'a' => 3,
                    'aa' => 4,
                ],
            ])
            ->returnsGenerator()
            ->returnsValue([
                [
                    'b' => 1,
                    'bb' => 2,
                ],
                [
                    'b' => 3,
                    'bb' => 4,
                ],
            ]);
    }
}
