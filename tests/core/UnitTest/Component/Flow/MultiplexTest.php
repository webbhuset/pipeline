<?php

namespace Webbhuset\Bifrost\Test\UnitTest\Component\Flow;

use Webbhuset\Bifrost\Component\Transform;

class MultiplexTest
{
    public static function __constructTest($test)
    {
    }

    public static function processTest($test)
    {
        $mapA = new Transform\Map(function($item) {
            return 'a';
        });
        $mapB = new Transform\Map(function($item) {
            return 'b';
        });
        $mapC = new Transform\Map(function($item) {
            return 'c';
        });

        $test->newInstance(
            function($item) {
                if ($item['v'] == 1) {
                    return 'a';
                } elseif ($item['v'] >= 3 && $item['v'] <= 5) {
                    return 'b';
                } elseif (in_array($item['v'], [2, 4, 6, 8, 10])) {
                    return 'c';
                }
            },
            [
                'a' => $mapA,
                'b' => $mapB,
                'c' => $mapC,
            ]
        );

        $testItems = [
            ['v' => 1], //a
            ['v' => 2], //c
            ['v' => 4], //b
            ['v' => 8], //c
            ['v' => 9], //
        ];

        $test->testThatArgs($testItems)
            ->returnsGenerator()
            ->returnsStrictValue([
                'a',
                'c',
                'b',
                'c',
            ]);
    }
}

