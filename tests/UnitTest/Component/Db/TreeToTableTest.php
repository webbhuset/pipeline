<?php

namespace Webbhuset\Bifrost\Test\UnitTest\Component\Db;

class TreeToTableTest
{
    public static function __constructTest($test)
    {
        $columns    = ['col1'];
        $dimensions = ['col1'];
        $static     = ['col1' => 1];
        $test->testThatArgs($columns, $dimensions, $static)
            ->throws('Webbhuset\\Bifrost\\Core\\BifrostException');

        $columns    = ['col2'];
        $dimensions = ['col1'];
        $static     = ['col1' => 1];
        $test->testThatArgs($columns, $dimensions, $static)
            ->throws('Webbhuset\\Bifrost\\Core\\BifrostException');
    }

    public static function processTest($test)
    {
        /**
         * @testCase Tree values and static values can be combined.
         */
        $columns = [
            'value_id',
            'entity_type_id',
            'attribute',
            'scope',
            'sku',
            'value',
        ];

        $treeDimensions = [
            'sku',
            'attribute',
            'scope',
            'value',
        ];
        $staticValues = [
            'value_id'       => null,
            'entity_type_id' => 4,
        ];
        $productsAsTree = [
            [
                '1011-11' => [
                    'name' => [
                        'en' => 'Name EN',
                        'sv' => 'Name SV',
                    ],
                    'price' => [
                        'usd' => 12,
                        'sek' => 120,
                    ],
                ],
            ],
            [
                '2022-22' => [
                    'name' => [
                        'en' => 'Name EN',
                        'sv' => 'Name SV',
                    ],
                    'price' => [
                        'usd' => 12,
                        'sek' => 120,
                    ],
                ],
            ],
        ];

        $test->newInstance($columns, $treeDimensions, $staticValues)
            ->testThatArgs($productsAsTree)
            ->returnsGenerator()
            ->returnsValue([
                [
                    'value_id'          => NULL,
                    'entity_type_id'    => 4,
                    'attribute'         => 'name',
                    'scope'             => 'en',
                    'sku'               => '1011-11',
                    'value'             => 'Name EN',
                ],
                [
                    'value_id'          => NULL,
                    'entity_type_id'    => 4,
                    'attribute'         => 'name',
                    'scope'             => 'sv',
                    'sku'               => '1011-11',
                    'value'             => 'Name SV',
                ],
                [
                    'value_id'          => NULL,
                    'entity_type_id'    => 4,
                    'attribute'         => 'price',
                    'scope'             => 'usd',
                    'sku'               => '1011-11',
                    'value'             => 12,
                ],
                [
                    'value_id'          => NULL,
                    'entity_type_id'    => 4,
                    'attribute'         => 'price',
                    'scope'             => 'sek',
                    'sku'               => '1011-11',
                    'value'             => 120,
                ],
                [
                    'value_id'          => NULL,
                    'entity_type_id'    => 4,
                    'attribute'         => 'name',
                    'scope'             => 'en',
                    'sku'               => '2022-22',
                    'value'             => 'Name EN',
                ],
                [
                    'value_id'          => NULL,
                    'entity_type_id'    => 4,
                    'attribute'         => 'name',
                    'scope'             => 'sv',
                    'sku'               => '2022-22',
                    'value'             => 'Name SV',
                ],
                [
                    'value_id'          => NULL,
                    'entity_type_id'    => 4,
                    'attribute'         => 'price',
                    'scope'             => 'usd',
                    'sku'               => '2022-22',
                    'value'             => 12,
                ],
                [
                    'value_id'          => NULL,
                    'entity_type_id'    => 4,
                    'attribute'         => 'price',
                    'scope'             => 'sek',
                    'sku'               => '2022-22',
                    'value'             => 120,
                ],
            ]);

        /**
         * @testCase If static values are defined as arrays they will be permuted.
         */
        $columns = [
            'de',
            'es',
        ];
        $tree = [[]];
        $static = [
            'es' => ['un', 'dos'],
            'de' => ['ein', 'zwie'],
        ];

        $test->newInstance($columns, $tree, $static)
            ->testThatArgs($tree)
            ->returnsGenerator()
            ->returnsValue([
                [
                    'de' => 'ein',
                    'es' => 'un',
                ],
                [
                    'de' => 'zwie',
                    'es' => 'un',
                ],
                [
                    'de' => 'ein',
                    'es' => 'dos',
                ],
                [
                    'de' => 'zwie',
                    'es' => 'dos',
                ],
            ]);

        /**
         * @testCase NULL is yielded if tree is not deep enough.
         */

        $columns = [
            'col1',
            'col2',
            'col3',
        ];
        $dimensions = [
            'col1',
            'col2',
            'col3',
        ];
        $tree = [
            [
                'level1' => 'value',
            ],
            [
                'level1' => [
                    'level2' => 'value',
                ],
            ],
            [
                'level1' => 'value',
            ],
        ];
        $test->newInstance($columns, $dimensions)
            ->testThatArgs($tree)
            ->returnsGenerator()
            ->returnsValue([
                null,
                [
                    'col1' => 'level1',
                    'col2' => 'level2',
                    'col3' => 'value',
                ],
                null,
            ]);
    }
}
