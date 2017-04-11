<?php
namespace Webbhuset\Bifrost\Test\UnitTest\Type;
use Webbhuset\Bifrost\Type as Core;

class HashmapTypeTest
{

    public static function __constructTest($test)
    {
        $params = [
            'key_type'   =>  new Core\StringType(),
            'value_type' =>  new Core\IntType(),
        ];
        $test->testThatArgs($params)->notThrows('Exception');

        $params = [
            'key_type'   =>  new Core\StringType(),
            'value_type' =>  new \stdClass(),
        ];
        $test->testThatArgs($params)
            ->throws('Webbhuset\Bifrost\BifrostException');
    }

    public static function diffTest($test)
    {
        $params = [
            'key_type'   =>  new Core\StringType(),
            'value_type' =>  new Core\IntType(),
        ];
        $test->newInstance($params);

        /* Test that two equal arrays returns empty diff */
        $old = [
            'string1'  => 143,
            'string2'  => 167,
        ];
        $new = [
            'string1'  => 143,
            'string2'  => 167,
        ];
        $expected = [
            '+' => [],
            '-' => [],
        ];
        $test->testThatArgs($old, $new)->returnsValue($expected);

        /* Test changed keys*/
        $old = [
            'string1'  => 143,
            'string3'  => 167,
        ];
        $new = [
            'string1'  => 143,
            'string2'  => 167,
        ];
        $expected = [
            '+' => ['string2'  => 167,],
            '-' => ['string3'  => 167,],
        ];
        $test->testThatArgs($old, $new)->returnsValue($expected);

        /* Test changed values */
        $old = [
            'string1'  => 143,
            'string2'  => 1600,
        ];
        $new = [
            'string1'  => 143,
            'string2'  => 167,
        ];
        $expected = [
            '+' => ['string2'  => 167,],
            '-' => ['string2'  => 1600,],
        ];
        $test->testThatArgs($old, $new)->returnsValue($expected);


    }
    public static function isEqualTest($test)
    {
        $params = [
            'key_type'   =>  new Core\StringType(),
            'value_type' =>  new Core\IntType(),
        ];
        $test->newInstance($params)
            ->testThatArgs(
                [
                    'string1'  => 143,
                    'string2'  => 167,
                ],
                [
                    'string2'  => 167,
                    'string1'  => 143
                ]
            )
            ->returnsValue(true)
            ->testThatArgs(
                [
                    'string1'  => 143,
                    'string2'  => 167,
                ],
                [
                    'string1'  => 143,
                    'string2'  => 167,
                    'string3'  => 14,
                ]
            )
            ->returnsValue(false)
            ->testThatArgs(
                [
                    'string1'  => 143,
                    'string2'  => 167,
                ],
                [
                    'string1'  => 143,
                    'string2'  => 0,
                ]
            )
            ->returnsValue(false)
            ->testThatArgs(
                [
                    '1'  => 143,
                    '2'  => 167,
                ],
                [
                    '1'  => 143,
                    2    => 167
                ]
            )
            ->returnsValue(true);
    }

    public static function getErrorsTest($test)
    {
        $params = [
            'key_type'   =>  new Core\StringType(),
            'value_type' =>  new Core\IntType(),
        ];
        $test->newInstance($params)
            ->testThatArgs(
                [
                    'string1'  => 143,
                    'string2'  => 167,
                ]
            )
            ->returnsValue(false)
            ->testThatArgs(
                [
                    'string1'  => '143',
                    'string2'  => 167,
                ]
            )
            ->notReturnsValue(false)
            ->testThatArgs(
                [
                    0          => 143,
                    'string2'  => 167,
                ]
            )
            ->notReturnsValue(false);


        $params = [
            'key_type'   =>  new Core\StringType(),
            'value_type' =>  new Core\IntType(),
            'min_size'   => 2,
            'max_size'   => 4,
        ];
        $test->newInstance($params)
            ->testThatArgs(
                [
                    'a' => 1,
                    'b' => 1,
                    'c' => 1,
                ]
            )
            ->returnsValue(false)
            ->testThatArgs(
                [
                    'a' => 1,
                ]
            )
            ->notReturnsValue(false)
            ->testThatArgs(
                [
                    'a' => 1,
                    'b' => 12,
                    'c' => 13,
                    'd' => 14,
                    'e' => 15,
                ]
            )
            ->notReturnsValue(false);
    }

    public static function castTest($test)
    {
        $params = [
            'key_type'   =>  new Core\StringType(),
            'value_type' =>  new Core\IntType(),
        ];

        $test->newInstance($params)
            ->testThatArgs(
                ['a' => 5]
            )
            ->returnsValue(
               ['a' => 5]
            )
            ->testThatArgs(
                [4 => '5']
            )
            ->returnsValue(
               ['4' => 5]
            )
            ->testThatArgs(
                ['4.986' => 5.0]
            )
            ->returnsValue(
               ['4.986' => 5]
            );
    }
}
