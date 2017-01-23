<?php
namespace Webbhuset\Bifrost\Core\Test\UnitTest\Type;
use Webbhuset\Bifrost\Core\Type as Core;

class EntityTypeTest
{

    public static function __constructTest($test)
    {
        $params = [
            'fields' => [
                'string_test' =>  new Core\StringType(),
                'int_test'    =>  new Core\IntType(),
            ]
        ];
        $test->testThatArgs($params)
            ->throws('Webbhuset\Bifrost\Core\BifrostException');

        $params = [
            'entity_id_field' => 'string_test',
            'fields' => [
                'string_test' =>  new Core\StringType(),
                'int_test'    =>  new Core\IntType(),
            ]
        ];
        $test->testThatArgs($params)
            ->notThrows('Webbhuset\Bifrost\Core\BifrostException');
    }

    public static function diffTest($test)
    {
        $params = [
            'entity_id_field' => 'id',
            'fields' => [
                'id'          => new Core\IntType(),
                'string_test' => new Core\StringType(),
                'int_test'    => new Core\IntType(),
            ]
        ];
        $test->newInstance($params);

        /* Test that two equal arrays returns empty diff */
        $old = [
            'id'          => 334,
            'string_test' => 'test',
            'int_test'    => 167,
        ];
        $new = [
            'id'          => 334,
            'string_test' => 'test',
            'int_test'    => 167,
        ];
        $expected = [];
        $test->testThatArgs($old, $new)->returnsValue($expected);


        /* Test that id is outputed even if id is the same */
        $old = [
            'id'          => 334,
            'string_test' => 'gammal test',
            'int_test'    => 167,
        ];
        $new = [
            'id'          => 334,
            'string_test' => 'ny test',
            'int_test'    => 167,
        ];
        $expected = [
            'string_test' => [
                '+' => 'ny test',
                '-' => 'gammal test'
            ],
            'id' => [
                '+' => 334,
                '-' => 334,
            ]
        ];
        $test->testThatArgs($old, $new)->returnsValue($expected);
    }

    public static function isEqualTest($test)
    {
        $params = [
            'entity_id_field' => 'id',
            'fields' => [
                'id'          => new Core\IntType(),
                'string_test' => new Core\StringType(),
                'int_test'    => new Core\IntType(),
            ]
        ];
        $test->newInstance($params)
            ->testThatArgs(
                [
                    'id'          => 334,
                    'string_test' => 'test',
                    'int_test'    => 167,
                ],
                [
                    'id'          => 334,
                    'string_test' => 'test',
                    'int_test'    => 167,
                ]
            )
            ->returnsValue(true)
            ->testThatArgs(
                [
                    'id'          => 334,
                    'string_test' => 'test',
                    'int_test'    => 167,
                ],
                [
                    'id'          => 334,
                    'string_test' => 'inte samma',
                    'int_test'    => 167,
                ]
            )
            ->returnsValue(false)
            ->testThatArgs(
                [
                    'id'          => 334,
                    'string_test' => 'test',
                    'int_test'    => 167,
                ],
                [
                    'id'          => 334,
                    'string_test' => 'test',
                    'int_test'    => 12346,
                ]
            )
            ->returnsValue(false);
    }

    public static function getErrorsTest($test)
    {
        $params = [
            'entity_id_field' => 'id',
            'fields' => [
                'id'          => new Core\IntType(),
                'string_test' => new Core\StringType(),
                'int_test'    => new Core\IntType(),
            ]
        ];
        $test->newInstance($params)
            ->testThatArgs(
                [
                    'id'          => 334,
                    'string_test' => 'test',
                    'int_test'    => 167,
                ]
            )
            ->returnsValue(false)
            ->testThatArgs(
                [
                    'id'          => 334,
                    'string_test' => 123,
                    'int_test'    => 167,
                ]
            )
            ->notReturnsValue(false)
            ->testThatArgs(
                [
                    'id'          => 334,
                    'string_test' => 'apa',
                    'int_test'    => 'elefant',
                ]
            )
            ->notReturnsValue(false)
            ->testThatArgs(
                [
                    'id'          => 334,
                    'string_test' => 'apa',
                    'int_test'    => '12',
                ]
            )
            ->notReturnsValue(false);
    }

    public static function castTest($test)
    {
    }
}
