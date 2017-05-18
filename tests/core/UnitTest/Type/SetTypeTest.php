<?php
namespace Webbhuset\Bifrost\Test\UnitTest\Type;

use Webbhuset\Bifrost\Type\TypeConstructor AS T;

class SetTypeTest
{
    public static function __constructTest($test)
    {
        $test->testThatArgs(T::Int())
            ->notThrows('Exception');

        $test->testThatArgs(new \stdClass)
            ->throws('Webbhuset\Bifrost\BifrostException');

        $params = [];
        $test->testThatArgs($params)
            ->throws('Webbhuset\Bifrost\BifrostException');
    }

    public static function diffTest($test)
    {
        $test->newInstance(T::Int());

        /* Test that two equal sets returns empty diff */
        $old = [11, 12, 13, 14, 15];
        $new = [11, 12, 13, 14, 15];
        $expected = [
            '+' => [],
            '-' => [],
        ];
        $test->testThatArgs($new, $old)->returnsValue($expected);

        /* Test that two equal sets returns empty diff */
        $old = [11, 12, 13, 14, 15];
        $new = [15, 14, 13, 12, 11];
        $expected = [
            '+' => [],
            '-' => [],
        ];
        $test->testThatArgs($new, $old)->returnsValue($expected);

        /* Test added element  */
        $old = [11, 12, 13, 14];
        $new = [15, 14, 13, 12, 11];
        $expected = [
            '+' => [15],
            '-' => [],
        ];
        $test->testThatArgs($new, $old)->returnsValue($expected);

        /* Test removed element  */
        $old = [11, 12, 13, 14, 15];
        $new = [15, 14, 13, 12];
        $expected = [
            '+' => [],
            '-' => [11],
        ];
        $test->testThatArgs($new, $old)->returnsValue($expected);


    }

    public static function isEqualTest($test)
    {
        $test->newInstance(T::Int())
            ->testThatArgs(
                [11, 12, 13, 14, 15],
                [11, 12, 13, 14, 15]
            )
            ->returnsValue(true)
            ->testThatArgs(
                [11, 12, 13, 14, 15],
                [15, 14, 13, 12, 11]
            )
            ->returnsValue(true)
            ->testThatArgs(
                [11, 12, 13, 14, 15],
                [10, 12, 13, 14, 15]
            )
            ->returnsValue(false)
            ->testThatArgs(
                [10, 12],
                [10, 12, 13, 14, 15]
            )
            ->returnsValue(false);

        $test->newInstance(T::String())
            ->testThatArgs(
                ['abc','bbb','edf'],
                ['abc','bbb','edf']
            )
            ->returnsValue(true)
            ->testThatArgs(
                ['abc','bbb','edf'],
                ['bbb','abc','edf']
            )
            ->returnsValue(true)
            ->testThatArgs(
                ['bbb','edf'],
                ['bbb','abc','edf']
            )
            ->returnsValue(false);

    }

    public static function getErrorsTest($test)
    {
        $test->newInstance(T::String())
            ->testThatArgs(['abc','bbb','edf'])->returnsValue(false)
            ->testThatArgs([])->returnsValue(false)
            ->testThatArgs(['abc', 123, 'edf'])->notReturnsValue(false)
            ->testThatArgs(['abc', false])->notReturnsValue(false);

        $test->newInstance(T::Int(), T::MIN(2), T::MAX(4))
            ->testThatArgs([1, 2, 3, 4])->returnsValue(false)
            ->testThatArgs([66, 77])->returnsValue(false)
            ->testThatArgs([1])->notReturnsValue(false)
            ->testThatArgs([1, 2, 3, 4, 5])->notReturnsValue(false);

    }

    public static function castTest($test)
    {
        $test->newInstance(T::String())
            ->testThatArgs([1, 2, 33.0, '4'])->returnsValue([1, 2, 33, 4]);
    }
}
