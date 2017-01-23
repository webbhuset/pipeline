<?php

namespace Webbhuset\Bifrost\Core\Test\UnitTest\Component\Flow;

use Webbhuset\Bifrost\Core\Component\Transform;
use Webbhuset\Bifrost\Core\Component\Flow;

class ForkTest
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
                $item['fork'] = 1;
                return $item;
            }),
            new Transform\Map(function($item) {
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
            new Transform\Map(function($item) {
                $item['fork'] = 1;
                return $item;
            }),
            new Transform\Reduce(function($carry, $item) {
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
            new Flow\Pipeline([
                new Transform\Map(function($item) {
                    $item['fork'] = 1;
                    return $item;
                }),
                new Transform\Reduce(function($carry, $item) {
                    $carry[] = $item['a'];

                    return $carry;
                }, []),
            ]),
            new Flow\Pipeline([
                new Transform\Map(function($item) {
                    return $item['a'] + 1;
                }),
                new Transform\Group(2),
            ]),
            new Flow\Pipeline([
                new Transform\Merge(
                    new Flow\Pipeline([
                        new Transform\Map(function($item) {
                            return [
                                'b' => $item['a'] * 10,
                            ];
                        }),
                        new Transform\Group(20),
                        new Transform\Expand(function($items) {
                            foreach ($items as $item) {
                                yield $item;
                            }
                        }),
                    ])
                ),
                new Transform\Map(function($item) {
                    return $item['b'];
                }),
                new Transform\Group(20),
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

