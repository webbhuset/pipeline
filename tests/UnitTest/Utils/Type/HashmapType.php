<?php
namespace Webbhuset\Bifrost\Test\Unit\Utils\Type;
use Webbhuset\Bifrost\Core\Utils\Type as Core;

class HashmapType extends \Webbhuset\Bifrost\Test\TestAbstract
    implements \Webbhuset\Bifrost\Test\TestInterface
{
    protected function diffTest()
    {
        $params = [
            'key_type'   =>  new Core\StringType(),
            'value_type' =>  new Core\IntType(),
        ];
        $this->newInstance($params);

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
        $this->testThatArgs($old, $new)->returns($expected);

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
        $this->testThatArgs($old, $new)->returns($expected);

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
        $this->testThatArgs($old, $new)->returns($expected);


    }
    protected function isEqualTest()
    {
        $params = [
            'key_type'   =>  new Core\StringType(),
            'value_type' =>  new Core\IntType(),
        ];
        $this->newInstance($params)
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
            ->returns(true)
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
            ->returns(false)
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
            ->returns(false)
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
            ->returns(true);
    }

    protected function getErrorsTest()
    {
        $params = [
            'key_type'   =>  new Core\StringType(),
            'value_type' =>  new Core\IntType(),
        ];
        $this->newInstance($params)
            ->testThatArgs(
                [
                    'string1'  => 143,
                    'string2'  => 167,
                ]
            )
            ->returns(false)
            ->testThatArgs(
                [
                    'string1'  => '143',
                    'string2'  => 167,
                ]
            )
            ->notReturns(false)
            ->testThatArgs(
                [
                    0          => 143,
                    'string2'  => 167,
                ]
            )
            ->notReturns(false);


        $params = [
            'key_type'   =>  new Core\StringType(),
            'value_type' =>  new Core\IntType(),
            'min_size'   => 2,
            'max_size'   => 4,
        ];
        $this->newInstance($params)
            ->testThatArgs(
                [
                    'a' => 1,
                    'b' => 1,
                    'c' => 1,
                ]
            )
            ->returns(false)
            ->testThatArgs(
                [
                    'a' => 1,
                ]
            )
            ->notReturns(false)
            ->testThatArgs(
                [
                    'a' => 1,
                    'b' => 12,
                    'c' => 13,
                    'd' => 14,
                    'e' => 15,
                ]
            )
            ->notReturns(false);
    }

    protected function castTest()
    {
        $params = [
            'key_type'   =>  new Core\StringType(),
            'value_type' =>  new Core\IntType(),
        ];

        $this->newInstance($params)
            ->testThatArgs(
                ['a' => 5]
            )
            ->returns(
               ['a' => 5]
            )
            ->testThatArgs(
                [4 => '5']
            )
            ->returns(
               ['4' => 5]
            )
            ->testThatArgs(
                ['4.986' => 5.0]
            )
            ->returns(
               ['4.986' => 5]
            );
    }
}
