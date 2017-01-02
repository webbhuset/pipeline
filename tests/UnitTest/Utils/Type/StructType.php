<?php
namespace Webbhuset\Bifrost\Test\Unit\Utils\Type;
use Webbhuset\Bifrost\Core\Utils\Type as Core;

class StructType extends \Webbhuset\Bifrost\Test\TestAbstract
    implements \Webbhuset\Bifrost\Test\TestInterface
{
    protected function diffTest()
    {
        $params = [
            'fields' => [
                'string_test' =>  new Core\StringType(),
                'int_test'    =>  new Core\IntType(),
            ]
        ];
        $this->newInstance($params);

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
        $this->testThatArgs($old, $new)->returns($expected);


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
        $this->testThatArgs($old, $new)->returns($expected);
    }

    protected function isEqualTest()
    {
        $params = [
            'fields' => [
                'string_test' =>  new Core\StringType(),
                'int_test'    =>  new Core\IntType(),
            ]
        ];
        $this->newInstance($params)
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
            ->returns(true)
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
            ->returns(false)
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
            ->returns(false);
    }

    protected function getErrorsTest()
    {
        $params = [
            'fields' => [
                'string_test' =>  new Core\StringType(),
                'int_test'    =>  new Core\IntType(),
            ]
        ];
        $this->newInstance($params)
            ->testThatArgs(
                [
                    'string_test' => 'test',
                    'int_test'    => 167,
                ]
            )
            ->returns(false)
            ->testThatArgs(
                [
                    'string_test' => 123,
                    'int_test'    => 167,
                ]
            )
            ->notReturns(false)
            ->testThatArgs(
                [
                    'string_test' => 'apa',
                    'int_test'    => 'elefant',
                ]
            )
            ->notReturns(false)
            ->testThatArgs(
                [
                    'string_test' => 'apa',
                    'int_test'    => '12',
                ]
            )
            ->notReturns(false);
    }

    protected function castTest()
    {
        $params = [
            'fields' => [
                'string_test' =>  new Core\StringType(),
                'int_test'    =>  new Core\IntType(),
            ]
        ];
        $this->newInstance($params)
            ->testThatArgs(
                [
                    'string_test' => 'test',
                    'int_test'    => 167,
                ]
            )
            ->returns(
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
            ->returns(
                [
                    'string_test' => '123',
                    'int_test'    => 167,
                ]
            );
    }
}
