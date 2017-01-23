<?php

namespace Webbhuset\Bifrost\Core\Test\UnitTest\Helper\ArrayHelper;

use Webbhuset\Bifrost\Core\Helper\ArrayHelper\KeyMapper;

class TreeTest
{
    public static function diffRecursiveTest($test)
    {
        /**
         * @testCase Elements from A that does not exist in B will be returned
         */
        $a          = ['a' => 1, 'b' => 1];
        $b          = ['b' => 1];
        $aNotInB    = ['a' => 1];

        $test->testThatArgs($a, $b)
            ->returnsStrictValue($aNotInB);

        /**
         * @testCase Values of leaf nodes are also compared.
         *           If a value in B is not equal to the same node in A it will be included.
         */
        $a          = ['a' => 1, 'b' => 1];
        $b          = ['a' => 2, 'b' => 1];
        $aNotInB    = ['a' => 1];

        $test->testThatArgs($a, $b)
            ->returnsStrictValue($aNotInB);

        /**
         * @testCase Comparisation is done recursively
         */
        $a = [
            'a' => [
                'b' => 1,
            ],
            'c' => [
                'd' => 1,
            ],
        ];

        $b = $a;
        $b['c']['d'] = 2;

        $aNotInB = [
            'c' => [
                'd' => 1,
            ],
        ];

        /**
         * @testCase Values that exists in B but not A is ignored.
         */
        $test->testThatArgs($a, $b)
            ->returnsStrictValue($aNotInB);

        $b = $a;
        $b['c']['e'] = 1;

        $aNotInB = [];

        $test->testThatArgs($a, $b)
            ->returnsStrictValue($aNotInB);
    }

    public static function buildRecursiveMapperTest($test)
    {
        $dataTree = [
            'sku-01' => [
                'name' => [
                    'en' => 'EN value',
                    'sv' => 'SV value',
                ],
                'price' => [
                    'USD' => 95,
                    'SEK' => 995,
                ],
            ],
            'sku-02' => [
                'name' => [
                    'en' => 'EN value',
                    'sv' => 'SV value',
                ],
                'price' => [
                    'USD' => 95,
                    'SEK' => 995,
                ],
            ],
        ];

        $mappedTree = [
            'new-sku-01' => [
                'new-name' => [
                    'new-en' => 'EN value',
                    'new-sv' => 'SV value',
                ],
                'new-price' => [
                    'new-USD' => 95,
                    'new-SEK' => 995,
                ],
            ],
            'new-sku-02' => [
                'new-name' => [
                    'new-en' => 'EN value',
                    'new-sv' => 'SV value',
                ],
                'new-price' => [
                    'new-USD' => 95,
                    'new-SEK' => 995,
                ],
            ],
        ];

        $root           = new KeyMapper(['sku-01' => 'new-sku-01', 'sku-02' => 'new-sku-02']);
        $attributeMap   = new KeyMapper(['name'   => 'new-name',   'price'  => 'new-price']);
        $langMap        = new KeyMapper(['en'     => 'new-en',     'sv'     => 'new-sv'], 'new-en');
        $currencyMap    = new KeyMapper(['USD'    => 'new-USD',    'SEK'    => 'new-SEK']);

        $children = [
            '*' => $attributeMap, [
                'name'  => $langMap,
                'price' => $currencyMap,
            ],
        ];

        $test->testThatArgs($root, $children)
            ->assertCallback(function($treeMapper) use ($dataTree, $mappedTree) {
                $mappedResult = $treeMapper->map($dataTree);

                if ($mappedResult != $mappedTree) {
                    return 'First Mapping did not return the expected result';
                }


                $flipped = $treeMapper->flip();
                $mappedBackAgain  = $flipped->map($mappedResult);
                if ($dataTree != $mappedBackAgain) {
                    return 'Data tree is not equal after being mapped back again.';
                }
            })
        ;
    }

}
