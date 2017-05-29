<?php

namespace Webbhuset\Bifrost\Test\UnitTest\Component\Flow;

use Webbhuset\Bifrost\Component\Transform;

class PipelineTest
{
    public static function __constructTest($test)
    {
        $test
            ->testThatArgs(['apa'])->throws('Webbhuset\\Bifrost\\Core\\BifrostException')
            ->testThatArgs([new \stdClass])->throws('Webbhuset\\Bifrost\\Core\\BifrostException')
        ;
    }

    public static function processTest($test)
    {
        $test->newInstance([
            new Transform\Map(function($item) {
                return [
                    'b' =>  $item['a'],
                ];
            }),
            new Transform\Map(function($item) {
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

