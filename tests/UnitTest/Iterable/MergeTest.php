<?php

namespace Webbhuset\Whaskell\Test\UnitTest\Iterable;

use Webbhuset\Whaskell\Iterable;
use Webbhuset\Whaskell\Flow;

class MergeTest
{
    public static function __constructTest($test)
    {

    }

    public static function __invokeTest($test)
    {
        /**
         * @testCase Test merge with multiple levels. Simulates default data.
         */
        $test->newInstance(
            new Iterable\Map(function($item) {
                return [
                    'b' => [
                        'c' => 0,
                        'e' => 1,
                    ],
                ];
            })
        );
        $items = [
            [
                'a' => 1,
                'b' => [
                    'c' => 2,
                    'd' => 3,
                ],
            ],
            [
                'a' => 4,
                'b' => [
                    'c' => 5,
                    'd' => 6,
                ],
            ],
        ];

        $test->testThatArgs($items)
            ->returnsGenerator()
            ->returnsStrictValue([
                [
                    'a' => 1,
                    'b' => [
                        'c' => 2,
                        'd' => 3,
                        'e' => 1,
                    ],
                ],
                [
                    'a' => 4,
                    'b' => [
                        'c' => 5,
                        'd' => 6,
                        'e' => 1,
                    ],
                ],
            ])
        ;

        /**
         * @testCase Test exception on item count mismatch.
         */
        $test->newInstance(
            new Iterable\Expand(function($item) {
                yield ['b' => 1];
                yield ['b' => 2];
            })
        );
        $items = [
            ['a' => 1],
            ['a' => 2],
        ];

        $test->testThatArgs($items)
            ->returnsGenerator()
            ->throws('Webbhuset\\Whaskell\\WhaskellException');

        /**
         * @testCase Test with reducer and expander. Simulates filling data from db.
         */
        $mockFetchFromDb = function($skus) {
            $rows = [];
            foreach ($skus as $sku) {
                if ($sku == '100-03') {
                    continue;
                }
                $id = (int) str_replace('100-', '', $sku);
                $rows[$sku] = [
                    'sku' => $sku,
                    'id'  => $id,
                ];
            }

            return $rows;
        };
        $test->newInstance(
            new Flow\Compose([
                new Iterable\Map(function($item) {
                    return $item['sku'];
                }),
                new Iterable\Group(2),
                new Iterable\Expand(function($skus) use ($mockFetchFromDb) {
                    $rows = $mockFetchFromDb($skus);

                    foreach ($skus as $sku) {
                        if (isset($rows[$sku])) {
                            $id = $rows[$sku]['id'];
                        } else {
                            $id = null;
                        }
                        yield [
                            'sku' => $sku,
                            'id' => $id,
                        ];
                    }
                }),
            ])
        );
        $items = [
            [
                'sku'  => '100-01',
                'name' => 'Product 1',
            ],
            [
                'sku'  => '100-02',
                'name' => 'Product 2',
            ],
            [
                'sku'  => '100-03',
                'name' => 'Product 3',
            ],
            [
                'sku'  => '100-04',
                'name' => 'Product 4',
            ],
        ];

        $test->testThatArgs($items)
            ->returnsGenerator()
            ->returnsStrictValue([
                [
                    'sku' => '100-01',
                    'name' => 'Product 1',
                    'id'  => 1,
                ],
                [
                    'sku' => '100-02',
                    'name' => 'Product 2',
                    'id'  => 2,
                ],
                [
                    'sku' => '100-03',
                    'name' => 'Product 3',
                    'id'  => null,
                ],
                [
                    'sku' => '100-04',
                    'name' => 'Product 4',
                    'id'  => 4,
                ],
            ])
        ;
    }
}
