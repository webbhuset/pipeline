<?php
namespace Webbhuset\Bifrost\Test\UnitTest\Type;

use Webbhuset\Bifrost\Type\TypeConstructor AS T;

class HashmapTypeTest
{
    public static function __constructTest($test)
    {
        $test->testThatArgs(T::String(), T::Int())->notThrows('Exception');

        $test->testThatArgs(T::String(), new \stdClass)
            ->throws('Webbhuset\Bifrost\BifrostException');
    }

    public static function diffTest($test)
    {
        $test->newInstance(T::String(), T::Int());

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
        $test->testThatArgs($new, $old)->returnsValue($expected);

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
        $test->testThatArgs($new, $old)->returnsValue($expected);

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
        $test->testThatArgs($new, $old)->returnsValue($expected);


    }
    public static function isEqualTest($test)
    {
        $test->newInstance(T::String(), T::Int())
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
        $test->newInstance(T::String(), T::Int())
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

        $test->newInstance(T::String(), T::Int(), T::MIN(2), T::MAX(4))
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
        $test->newInstance(T::String(), T::Int())
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
