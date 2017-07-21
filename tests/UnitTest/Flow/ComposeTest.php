<?php

namespace Webbhuset\Whaskell\Test\UnitTest\Flow;

use Webbhuset\Whaskell\Iterable;

class ComposeTest
{
    public static function __constructTest($test)
    {
        $test
            ->testThatArgs(['apa'])->throws('Webbhuset\\Whaskell\\WhaskellException')
            ->testThatArgs([new \stdClass])->throws('Webbhuset\\Whaskell\\WhaskellException')
        ;
    }

    public static function __invokeTest($test)
    {
        $test->newInstance([
            new Iterable\Map(function($item) {
                return [
                    'b' =>  $item['a'],
                ];
            }),
            new Iterable\Map(function($item) {
                return [
                    'c' =>  $item['b'],
                ];
            })
        ]);

        $testItems = [
            ['a' => 1],
            ['a' => 2],
        ];

        $test->testThatArgs($testItems)
            ->returnsGenerator()
            ->returnsStrictValue([
                ['c' => 1],
                ['c' => 2],
            ]);
    }
}

