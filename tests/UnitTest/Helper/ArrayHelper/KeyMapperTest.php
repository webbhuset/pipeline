<?php

namespace Webbhuset\Bifrost\Test\UnitTest\Helper\ArrayHelper;

use stdClass;
use Webbhuset\Bifrost\Helper\ArrayHelper\KeyMapper;

class KeyMapperTest
{
    public static function __constructTest($test)
    {
        $test
            ->testThatArgs(['a' => []])
            ->throws('Webbhuset\\Bifrost\\Core\\Type\\TypeException')
            ->testThatArgs(['a' => new stdClass])
            ->throws('Webbhuset\\Bifrost\\Core\\Type\\TypeException');
    }

    public static function mapTest($test)
    {
        /**
         * @testCase Keys will be renamed according to the $map.
         */
        $array = [
            'oldKey'        => 'a value',
            'anotherOldKey' => 'another value',
        ];

        $map = [
            'oldKey'        => 'newKey',
            'anotherOldKey' => 'anotherNewKey',
        ];

        $test->newInstance($map)
            ->testThatArgs($array)
            ->returnsValue([
                'newKey'        => 'a value',
                'anotherNewKey' => 'another value',
            ]);

        /**
         * @testCase Keys can safely swap names.
         */
        $array = [
            'name'      => 'a name',
            'price'     => 123,
        ];

        $map = [
            'name'  => 'price',
            'price' => 'name',
        ];

        $test->newInstance($map)
            ->testThatArgs($array)
            ->returnsValue([
                'price'     => 'a name',
                'name'      => 123,
            ]);


        /**
         * @testCase FILTER: Keys that does not exist in the map will be left alone.
         */

        $array = [
            'name'      => 'a name',
            'price'     => 123,
            'notInMap'  => 'value',
        ];

        $map = [
            'name'  => 71,
            'price' => 95,
        ];

        $test->newInstance($map)
            ->testThatArgs($array)
            ->returnsValue([
                71          => 'a name',
                95          => 123,
                'notInMap'  => 'value',
            ]);

        /**
         * @testCase FILTER: If you want to removed non mapped elements, set the second parameter to TRUE
         */

        $array = [
            'name'      => 'a name',
            'price'     => 123,
            'notInMap'  => 'value',
        ];

        $map = [
            'name'  => 71,
            'price' => 95,
        ];

        $test->newInstance($map)
            ->testThatArgs($array, true)
            ->returnsValue([
                71          => 'a name',
                95          => 123,
            ]);

        /**
         * @testCase KEY COLLISIONS: By default a rename will not overwrite.
         */
        $array = [
            'key'       => 'value',
            'otherKey'  => 'other value',
        ];

        $map = [
            'key'  => 'otherKey',
        ];

        $test->newInstance($map)
            ->testThatArgs($array)
            ->returnsValue([
                'otherKey'  => 'other value',
            ]);

        /**
         * @testCase KEY COLLISION: If you want a rename to overwrite, pass TRUE to the constructor.
         */
        $array = [
            'key'       => 'value',
            'otherKey'  => 'other value',
        ];

        $map = [
            'key'  => 'otherKey',
        ];

        $test->newInstance($map)
            ->testThatArgs($array, false, true)
            ->returnsValue([
                'otherKey'  => 'value',
            ]);

        /**
         * @testCase You can add default map value for non array value
         */

        $array = 'value';
        $map = [
            'key'  => 'otherKey',
        ];

        $test->newInstance($map, 'otherKey')
            ->testThatArgs($array)
            ->returnsValue([
                'otherKey'  => 'value',
            ]);

        /**
         * @testCase Recursive Map: You can add children to your mapper. This allows you to map several levels.
         */

        $dataTree = [
            'name' => [
                'en' => 'EN value',
                'sv' => 'SV value',
            ],
            'price' => [
                'USD' => 95,
                'SEK' => 995,
            ],
        ];

        $attributeMap   = [
            'name'  => 75,
            'price' => 95
        ];
        $langMap = new KeyMapper([
            'en'  => 3,
            'sv'  => 2
        ]);
        $currencyMap = new KeyMapper([
            'USD' => 5,
            'SEK' => 4
        ]);

        $children = [
            'name'  => $langMap,
            'price' => $currencyMap
        ];

        $test->newInstance($attributeMap, null, $children)
            ->testThatArgs($dataTree)
            ->returnsValue([
                75 => [
                    3 => 'EN value',
                    2 => 'SV value',
                ],
                95 => [
                    5 => 95,
                    4 => 995,
                ],
            ]) ;

        /**
         * @testCase Recursive Map: You can use a wildcard for children mappers.
         *           By default this is *, but it can be changed to anything with the 4rd constructor param.
         */

        $product = [
            'name' => [
                'en' => 'EN Name',
                'sv' => 'SV Name',
            ],
            'description' => [
                'en' => 'EN Desc',
                'sv' => 'SV Desc',
            ],
        ];

        $langMap = new KeyMapper([
            'en'  => 3,
            'sv'  => 2
        ]);
        $attributeMap   = [
            'name'          => 75,
            'description'   => 95
        ];

        $wildcard = '#';
        $children = [
            '#' => $langMap,
        ];

        $test->newInstance($attributeMap, null, $children, $wildcard)
            ->testThatArgs($product)
            ->returnsValue([
                75 => [
                    3 => 'EN Name',
                    2 => 'SV Name',
                ],
                95 => [
                    3 => 'EN Desc',
                    2 => 'SV Desc',
                ],
            ]);
    }

    public static function flipTest($test)
    {
        /**
         * @testCase You can flip the mapper to map back and fourth between arrays.
         *           Note that this will create and return a new mapper object.
         */
        $array = [
            'name'  => 'a name',
            'price' => '100kr',
        ];

        $mappedArray = [
            71 => 'a name',
            95 => '100kr',
        ];

        $map = [
            'name'  => 71,
            'price' => 95,
        ];

        $test->newInstance($map)
            ->testThatArgs()
            ->returnsObject()
            ->assertCallback(function($flippedMapper, $mapper) use ($array, $mappedArray) {
                $result = $flippedMapper->map($mappedArray);

                if ($array != $result) {
                    return 'Results from flipped mapper does not match';
                }
            });
    }

    public static function addChildrenTest($test)
    {
    }
}
