<?php

namespace Webbhuset\Whaskell\Test\UnitTest\Flow;

use Webbhuset\Whaskell\Iterable;
use Webbhuset\Whaskell\Flow;

class ForkTest
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
                $item['fork'] = 1;
                return $item;
            }),
            new Iterable\Map(function($item) {
                $item['fork'] = 2;
                return $item;
            })
        ]);

        $testItems = [
            ['a' => 1],
        ];

        $test->testThatArgs($testItems)
            ->returnsGenerator()
            ->returnsStrictValue([
                ['a' => 1, 'fork' => 1],
                ['a' => 1, 'fork' => 2],
            ]);

        $test->newInstance([
            new Iterable\Map(function($item) {
                $item['fork'] = 1;
                return $item;
            }),
            new Iterable\Reduce(function($carry, $item) {
                $carry[] = $item['a'];

                return $carry;
            }, [])
        ]);

        $testItems = [
            ['a' => 1],
            ['a' => 2],
        ];

        $test->testThatArgs($testItems)
            ->returnsGenerator()
            ->returnsStrictValue([
                ['a' => 1, 'fork' => 1],
                ['a' => 2, 'fork' => 1],
                [1, 2],
            ]);

        $test->newInstance([
            new Flow\Compose([
                new Iterable\Map(function($item) {
                    $item['fork'] = 1;
                    return $item;
                }),
                new Iterable\Reduce(function($carry, $item) {
                    $carry[] = $item['a'];

                    return $carry;
                }, []),
            ]),
            new Flow\Compose([
                new Iterable\Map(function($item) {
                    return $item['a'] + 1;
                }),
                new Iterable\Group(2),
            ]),
            new Flow\Compose([
                new Iterable\Merge(
                    new Flow\Compose([
                        new Iterable\Map(function($item) {
                            return [
                                'b' => $item['a'] * 10,
                            ];
                        }),
                        new Iterable\Group(20),
                        new Iterable\Expand(function($items) {
                            foreach ($items as $item) {
                                yield $item;
                            }
                        }),
                    ])
                ),
                new Iterable\Map(function($item) {
                    return $item['b'];
                }),
                new Iterable\Group(20),
            ])
        ]);

        $test->testThatArgs($testItems)
            ->returnsGenerator()
            ->returnsStrictValue([
                [1, 2],
                [2, 3],
                [10, 20],
            ]);
    }
}

