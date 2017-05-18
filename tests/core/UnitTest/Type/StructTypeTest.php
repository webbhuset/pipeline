<?php
namespace Webbhuset\Bifrost\Test\UnitTest\Type;

use Webbhuset\Bifrost\Type\TypeConstructor AS T;

class StructTypeTest
{
    public static function __constructTest($test)
    {
        $params = [
            'string_test' =>  T::String(),
            'int_test'    =>  T::Int(),
        ];
        $test->testThatArgs($params)
            ->notThrows('Exception');

        $params = ['fields' => 'apa'];
        $test->testThatArgs($params)
            ->throws('Webbhuset\Bifrost\BifrostException');

        $params = [];
        $test->testThatArgs($params)
            ->throws('Webbhuset\Bifrost\BifrostException');
    }

    public static function diffTest($test)
    {
        $params = [
            'string_test' =>  T::String(),
            'int_test'    =>  T::Int(),
        ];
        $test->newInstance($params);

        /* Test that two equal arrays returns empty diff */
        $old = [
            'string_test' => 'test',
            'int_test'    => 167,
        ];
        $new = [
            'string_test' => 'test',
            'int_test'    => 167,
        ];
        $expected = [];
        $test->testThatArgs($old, $new)->returnsValue($expected);


        /* Test that two equal arrays returns empty diff */
        $old = [
            'string_test' => 'gammal test',
            'int_test'    => 167,
        ];
        $new = [
            'string_test' => 'ny test',
            'int_test'    => 167,
        ];
        $expected = [
            'string_test' => [
                '+' => 'ny test',
                '-' => 'gammal test'
            ],
        ];
        $test->testThatArgs($new, $old)->returnsValue($expected);
    }

    public static function isEqualTest($test)
    {
        $params = [
            'string_test' =>  T::String(),
            'int_test'    =>  T::Int(),
        ];

        $test->newInstance($params)
            ->testThatArgs(
                [
                    'string_test' => 'test',
                    'int_test'    => 167,
                ],
                [
                    'string_test' => 'test',
                    'int_test'    => 167,
                ]
            )
            ->returnsValue(true)
            ->testThatArgs(
                [
                    'string_test' => 'test',
                    'int_test'    => 167,
                ],
                [
                    'string_test' => 'inte samma',
                    'int_test'    => 167,
                ]
            )
            ->returnsValue(false)
            ->testThatArgs(
                [
                    'string_test' => 'test',
                    'int_test'    => 167,
                ],
                [
                    'string_test' => 'test',
                    'int_test'    => 12346,
                ]
            )
            ->returnsValue(false);
    }

    public static function getErrorsTest($test)
    {
        $params = [
            'string_test' =>  T::String(),
            'int_test'    =>  T::Int(),
        ];

        $test->newInstance($params)
            ->testThatArgs(
                [
                    'string_test' => 'test',
                    'int_test'    => 167,
                ]
            )
            ->returnsValue(false)
            ->testThatArgs(
                [
                    'string_test' => 123,
                    'int_test'    => 167,
                ]
            )
            ->notReturnsValue(false)
            ->testThatArgs(
                [
                    'string_test' => 'apa',
                    'int_test'    => 'elefant',
                ]
            )
            ->notReturnsValue(false)
            ->testThatArgs(
                [
                    'string_test' => 'apa',
                    'int_test'    => '12',
                ]
            )
            ->notReturnsValue(false);
    }

    public static function castTest($test)
    {
        $params = [
            'string_test' =>  T::String(),
            'int_test'    =>  T::Int(),
        ];
        $test->newInstance($params)
            ->testThatArgs(
                [
                    'string_test' => 'test',
                    'int_test'    => 167,
                ]
            )
            ->returnsValue(
                [
                    'string_test' => 'test',
                    'int_test'    => 167,
                ]
            )
            ->testThatArgs(
                [
                    'string_test' => 123,
                    'int_test'    => '167',
                ]
            )
            ->returnsValue(
                [
                    'string_test' => '123',
                    'int_test'    => 167,
                ]
            );
    }
}
