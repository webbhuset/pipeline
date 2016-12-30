<?php
namespace Webbhuset\Bifrost\Test\Unit\Utils\Type;
use Webbhuset\Bifrost\Core\Utils\Type as Core;

class EntityType extends \Webbhuset\Bifrost\Test\TestAbstract
    implements \Webbhuset\Bifrost\Test\TestInterface
{
    protected function diffTest()
    {
        $params = [
            'entity_id_field' => 'id',
            'fields' => [
                'id'          => new Core\IntType(),
                'string_test' => new Core\StringType(),
                'int_test'    => new Core\IntType(),
            ]
        ];
        $this->newInstance($params);

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
        $this->testThatArgs($old, $new)->returns($expected);


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
            'id'          => 334,
        ];
        $this->testThatArgs($old, $new)->returns($expected);
    }

    protected function isEqualTest()
    {
        $params = [
            'entity_id_field' => 'id',
            'fields' => [
                'id'          => new Core\IntType(),
                'string_test' => new Core\StringType(),
                'int_test'    => new Core\IntType(),
            ]
        ];
        $this->newInstance($params)
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
            ->returns(true)
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
            ->returns(false)
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
            ->returns(false);
    }

    protected function getErrorsTest()
    {
        $params = [
            'entity_id_field' => 'id',
            'fields' => [
                'id'          => new Core\IntType(),
                'string_test' => new Core\StringType(),
                'int_test'    => new Core\IntType(),
            ]
        ];
        $this->newInstance($params)
            ->testThatArgs(
                [
                    'id'          => 334,
                    'string_test' => 'test',
                    'int_test'    => 167,
                ]
            )
            ->returns(false)
            ->testThatArgs(
                [
                    'id'          => 334,
                    'string_test' => 123,
                    'int_test'    => 167,
                ]
            )
            ->notReturns(false)
            ->testThatArgs(
                [
                    'id'          => 334,
                    'string_test' => 'apa',
                    'int_test'    => 'elefant',
                ]
            )
            ->notReturns(false)
            ->testThatArgs(
                [
                    'id'          => 334,
                    'string_test' => 'apa',
                    'int_test'    => '12',
                ]
            )
            ->notReturns(false);
    }

    protected function castTest()
    {
    }
}
