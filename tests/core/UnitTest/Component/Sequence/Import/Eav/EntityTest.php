<?php

namespace Webbhuset\Bifrost\Core\Test\UnitTest\Component\Sequence\Import\Eav;

use Webbhuset\Bifrost\Core\Component;
use Webbhuset\Bifrost\Core\Test\Helper\Component\AssertComponent;
use Exception;

class EntityTest
{
    public static function __constructTest($test)
    {
        $almostValidConfig = [
            'attributesByType' => ['1' => [[]]],
            'attributeSetsByName' => ['1' => [[]]],
            'valueTableConfig' => [
                'columns'       => [1],
                'dimensions'    => [1],
                'static'        => ['sd' => []],
            ],
            'idFieldName'   => 1,
            'setFieldName'  => 1,
            'batchSize'     => 1,
            'defaultScope'  => [],
        ];

        $test->testThatArgs($almostValidConfig)
            ->throws('Webbhuset\\Bifrost\\Core\\Type\\TypeException');

        $validConfig = [
            'attributesByType' => ['1' => ['']],
            'attributeSetsByName' => ['1' => ['']],
            'valueTableConfig' => [
                'columns'       => [''],
                'dimensions'    => ['a'],
                'static'        => ['sd' => 1],
            ],
            'idFieldName'   => 'id',
            'setFieldName'  => 'set',
            'batchSize'     => 2,
            'defaultScope'  => 0,
        ];

        $test->testThatArgs($validConfig)
            ->notThrows('Exception');
    }

    public static function processTest($test)
    {

    }

    public static function handleAttributeValuesTest($test)
    {
        /**
         * @testCase The items will be converted to a tree and then flattened to table rows.
         */
        $config = [
            'attributesByType'      => [
                'type_a' => ['ta_sf', 'ta_sg', 'ta_sAll', 'ta_sNone'],
                'type_b' => ['tb_sf', 'tb_sg', 'tb_sAll', 'tb_sNone'],
            ],
            'attributeSetsByName'   => [
                'set_f' => ['ta_sf', 'tb_sf', 'ta_sAll', 'tb_sAll'],
                'set_g' => ['ta_sg', 'tb_sg', 'ta_sAll', 'tb_sAll'],
            ],
            'valueTableConfig'      => [
                'columns'       => ['type', 'attribute', 'scope', 'entity', 'value'],
                'dimensions'    => ['entity', 'attribute', 'scope', 'value'],
                'static'        => ['type' => ['item']],
            ],
            'idFieldName'           => 'id',
            'setFieldName'          => 'set',
            'batchSize'             => 10,
        ];
        $items = [
            [
                'id'        => 'itm_sf_11',
                'set'       => 'set_f',
                'ta_sf'     => [0 => 'itm_sf_11/ta_sf/0/v0'],
                'tb_sf'     => [1 => 'itm_sf_11/tb_sf/1/v1'],
                'ta_sg'     => [2 => 'itm_sf_11/ta_sg/2/v2'],
                'tb_sg'     => [3 => 'itm_sf_11/tb_sg/3/v3'],
                'ta_sAll'   => [4 => 'itm_sf_11/ta_sAll/4/v4'],
                'tb_sAll'   => [5 => 'itm_sf_11/tb_sAll/5/v5'],
                'ta_sNone'  => [6 => 'itm_sf_11/ta_sNone/6/v6'],
                'tb_sNone'  => [7 => 'itm_sf_11/tb_sNone/7/v7'],
            ],
            [
                'id'        => 'itm_sg_22',
                'set'       => 'set_g',
                'ta_sf'     => [0 => 'itm_sg_22/ta_sf/0/v0'],
                'tb_sf'     => [1 => 'itm_sg_22/tb_sf/1/v1'],
                'ta_sg'     => [2 => 'itm_sg_22/ta_sg/2/v2'],
                'tb_sg'     => [3 => 'itm_sg_22/tb_sg/3/v3'],
                'ta_sAll'   => [4 => 'itm_sg_22/ta_sAll/4/v4'],
                'tb_sAll'   => [5 => 'itm_sg_22/tb_sAll/5/v5'],
                'ta_sNone'  => [6 => 'itm_sg_22/ta_sNone/6/v6'],
                'tb_sNone'  => [7 => 'itm_sg_22/tb_sNone/7/v7'],
            ],
        ];
        $expected = [
          [
            'type' => 'type_a',
            'rows' => [
              [
                'type'      => 'item',
                'attribute' => 'ta_sf',
                'scope'     => 0,
                'entity'    => 'itm_sf_11',
                'value'     => 'itm_sf_11/ta_sf/0/v0',
              ],
              [
                'type'      => 'item',
                'attribute' => 'ta_sAll',
                'scope'     => 4,
                'entity'    => 'itm_sf_11',
                'value'     => 'itm_sf_11/ta_sAll/4/v4',
              ],
              [
                'type'      => 'item',
                'attribute' => 'ta_sg',
                'scope'     => 2,
                'entity'    => 'itm_sg_22',
                'value'     => 'itm_sg_22/ta_sg/2/v2',
              ],
              [
                'type'      => 'item',
                'attribute' => 'ta_sAll',
                'scope'     => 4,
                'entity'    => 'itm_sg_22',
                'value'     => 'itm_sg_22/ta_sAll/4/v4',
              ],
            ],
          ],
          [
            'type' => 'type_b',
            'rows' => [
              [
                'type'      => 'item',
                'attribute' => 'tb_sf',
                'scope'     => 1,
                'entity'    => 'itm_sf_11',
                'value'     => 'itm_sf_11/tb_sf/1/v1',
              ],
              [
                'type'      => 'item',
                'attribute' => 'tb_sAll',
                'scope'     => 5,
                'entity'    => 'itm_sf_11',
                'value'     => 'itm_sf_11/tb_sAll/5/v5',
              ],
              [
                'type'      => 'item',
                'attribute' => 'tb_sg',
                'scope'     => 3,
                'entity'    => 'itm_sg_22',
                'value'     => 'itm_sg_22/tb_sg/3/v3',
              ],
              [
                'type'      => 'item',
                'attribute' => 'tb_sAll',
                'scope'     => 5,
                'entity'    => 'itm_sg_22',
                'value'     => 'itm_sg_22/tb_sAll/5/v5',
              ],
            ],
          ],
        ];
        $monad = [
            'insertAttributeValues' => function($rows, $type) {
                return [
                    'type' => $type,
                    'rows' => $rows
                ];
            },
        ];

        $test->newInstance(self::getEmptyConfig())
            ->testThatArgs($config)
            ->assertCallback(AssertComponent::makeAssert($items, $expected, $monad));
    }

    public static function compareWithOldValuesTest($test)
    {
        $treeItems = [
            [11 => ['all' => ['oldValue'], 'a' => ['newValue']]],
            [22 => ['all' => ['oldValue'], 'b' => ['newValue']]],
        ];
        $attributes = ['all', 'a', 'b'];
        $expected = [
            11 => ['a' => [0 => 'newValue']],
            22 => ['b' => [0 => 'newValue']],
        ];
        $monad = [
            'fetchAttributeValues' => function($ids, $type, $attributes) {
                $rows = [];
                foreach ($ids as $id) {
                    foreach ($attributes as $code) {
                        $rows[] = [
                            'static'    => 1,
                            'attribute' => $code,
                            'scope'     => 0,
                            'entity'    => $id,
                            'value'     => 'oldValue',
                        ];
                    }
                }

                return $rows;
            },
        ];
        $config = self::getEmptyConfig();
        $config['valueTableConfig']['dimensions'] = ['entity', 'attribute', 'scope', 'value'];

        $test->newInstance(self::getEmptyConfig())
            ->testThatArgs($config, $attributes, 'type_a')
            ->assertCallback(AssertComponent::makeAssert($treeItems, [$expected], $monad));
    }

    public static function batchAndMergeResultTest($test)
    {
        /**
         * @testCase Id will merged in to items.
         */

        $ids = [
            ['id' => 11],
            ['id' => null],
            ['id' => 22],
        ];
        $monad = [
            'getEntityIds' => function($items) use (&$ids) {
                return array_splice($ids, 0, count($items));
            },
        ];
        $items = [
            ['sku' => 'AAA'],
            ['sku' => 'BBB'],
            ['sku' => 'CCC'],
        ];
        $expected = [
            ['sku' => 'AAA', 'id' => 11],
            ['sku' => 'BBB', 'id' => null],
            ['sku' => 'CCC', 'id' => 22],
        ];

        $test->newInstance(self::getEmptyConfig())
            ->testThatArgs(2, 'getEntityIds')
            ->returnsObject()
            ->assertCallback(AssertComponent::makeAssert($items, $expected, $monad));
    }

    public static function filterByColumnValueTest($test)
    {
        /**
         * @testCase Items will be filtered using a column and a value. The equals (==) operator is used.
         */
        $items = [
            ['id' => 11],
            ['id' => null],
            ['id' => 22],
        ];
        $expected = [
            ['id' => 11],
            ['id' => 22],
        ];

        $test->newInstance(self::getEmptyConfig())
            ->testThatArgs('id', true)
            ->assertCallback(AssertComponent::makeAssert($items, $expected));

        $expected = [
            ['id' => null],
        ];

        $test->newInstance(self::getEmptyConfig())
            ->testThatArgs('id', null)
            ->assertCallback(AssertComponent::makeAssert($items, $expected));
    }

    public static function fillAttributeNullValuesTest($test)
    {
        /**
         * @testCase All non existsing keys will be filled with array of null ([null]) values.
         */
        $items = array_fill(0, 2, [
            'set'   => 'a',
            'c'     => 1,
            'd'     => 2,
        ]);
        $items[1]['set'] = 'b';

        $sets = [
            'a' => ['all', 'a'],
            'b' => ['all', 'b'],
        ];

        $expected = [
            [
                'all'   => [0 => null],
                'a'     => [0 => null],
                'set'   => 'a',
                'c'     => 1,
                'd'     => 2,
            ],
            [
                'all'   => [0 => null],
                'b'     => [0 => null],
                'set'   => 'b',
                'c'     => 1,
                'd'     => 2,
            ],
        ];

        $test->newInstance(self::getEmptyConfig())
            ->testThatArgs($sets, 'set')
            ->assertCallback(AssertComponent::makeAssert($items, $expected));
    }

    public static function prepareTreeTest($test)
    {
        /**
         * @testCase Items will mapped as a tree with levels [id, attribute, scope, value].
         *           Items are also filtered on the attributes in the corresponding attribute set.
         */
        $items = [
            ['id' => 11, 'all' => ['f'], 'set' => 'a', 'a' => [1], 'b' => [3], 'other' => [5]],
            ['id' => 22, 'all' => ['g'], 'set' => 'b', 'a' => [2], 'b' => [4], 'other' => [6]],
        ];

        $sets = [
            'a' => array_flip(['all', 'a']),
            'b' => array_flip(['all', 'b']),
        ];

        $expected = [
            [11 => ['all' => ['f'], 'a' => [1]]],
            [22 => ['all' => ['g'], 'b' => [4]]],
        ];

        $test->newInstance(self::getEmptyConfig())
            ->testThatArgs('id', 'set', $sets)
            ->assertCallback(AssertComponent::makeAssert($items, $expected));

        /**
         * @testCase If an attribute set is empty an empty item will be returned.
         */
        $emptySets = [
            'a' => [],
            'b' => [],
        ];
        $twoEmptyItems = [[], []];

        $test->newInstance(self::getEmptyConfig())
            ->testThatArgs('id', 'set', $emptySets)
            ->assertCallback(AssertComponent::makeAssert($items, $twoEmptyItems));
    }

    public static function intersectSetsTest($test)
    {
        /**
         * @testCase The returned sets will be an intersection of the attributes in the set and attributes.
         */
        $sets = [
            'a' => ['all', 'a', 'other'],
            'b' => ['all', 'b', 'other'],
        ];
        $attributes = ['all', 'a', 'b'];

        $intersectionOfSetsAndAttributes = [
            'a' => array_flip(['all', 'a']),
            'b' => array_flip(['all', 'b']),
        ];

        $test->newInstance(self::getEmptyConfig())
            ->testThatArgs($sets, $attributes)
            ->returnsValue($intersectionOfSetsAndAttributes);

        /**
         * @testCase If attributes are empty the sets will be empty.
         */
        $attributes = [];
        $emptySets = ['a' => [], 'b' => []];
        $test
            ->testThatArgs($sets, $attributes)
            ->returnsValue($emptySets);
    }

    protected static function getEmptyConfig()
    {
        return [
            'attributesByType' => ['1' => ['']],
            'attributeSetsByName' => ['1' => ['']],
            'valueTableConfig' => [
                'columns'       => [''],
                'dimensions'    => ['a'],
                'static'        => ['sd' => 1],
            ],
            'idFieldName'   => 'id',
            'setFieldName'  => 'set',
            'batchSize'     => 2,
            'defaultScope'  => 0,
        ];
    }
}
