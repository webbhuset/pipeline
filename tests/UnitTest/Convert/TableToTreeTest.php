<?php

namespace Webbhuset\Whaskell\Test\UnitTest\Db;

use Webbhuset\Whaskell\Db;

class TableToTreeTest
{
    public static function __constructTest($test)
    {
        $test->testThatArgs(['one'])
            ->throws('Webbhuset\\Whaskell\\\WhaskellException');
    }

    public static function __invokeTest($test)
    {
        /**
         * @testCase The treeToTable and tableToTree components are isomorphic.
         */
        $dimensions = [
            'sku',
            'attribute',
            'scope',
            'value'
        ];

        $columns = [
            'attribute',
            'scope',
            'sku',
            'value',
        ];

        $treeToTable = new Db\TreeToTable($columns, $dimensions);

        $tree = [
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
        ];

        $rows = iterator_to_array($treeToTable->process([$tree]));

        $test->newInstance($dimensions)
            ->testThatArgs([$rows])
            ->returnsGenerator()
            ->returnsValue([$tree])
        ;

        /**
         * @testCase A subset of row columns can be mapped.
         */
        $dimensions = [
            'sku',
            'value',
        ];

        $test->newInstance($dimensions)
            ->testThatArgs([$rows])
            ->returnsGenerator()
            ->returnsValue([
                [
                    '1011-11' => 120,
                    '2022-22' => 120,
                ]
            ]);

        /**
         * @TestCase If rows are broken a partial tree will be yielded.
         */
        $brokenRows = [
            [
                'attribute'         => 'name',
                'scope'             => 'en',
                'sku'               => '1011-11',
                'value'             => 'Name EN',
            ],
            [
                'scope'             => 'sv',
                'sku'               => '2022-22',
                'value'             => 'Name SV',
            ],
        ];

        $dimensions = [
            'sku',
            'attribute',
            'scope',
            'value',
        ];

        $test->newInstance($dimensions)
            ->testThatArgs([$brokenRows])
            ->returnsGenerator()
            ->returnsValue([
                [
                    '1011-11' => [
                        'name' => [
                            'en' => 'Name EN',
                        ],
                    ],
                    '2022-22' => [],
                ],
            ]);
    }
}
