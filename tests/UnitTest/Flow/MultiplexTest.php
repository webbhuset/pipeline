<?php

namespace Webbhuset\Whaskell\Test\UnitTest\Flow;

use Webbhuset\Whaskell\Iterable;

class MultiplexTest
{
    public static function __constructTest($test)
    {
    }

    public static function __invokeTest($test)
    {
        $mapA = new Iterable\Map(function($item) {
            return 'a';
        });
        $mapB = new Iterable\Map(function($item) {
            return 'b';
        });
        $mapC = new Iterable\Map(function($item) {
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

