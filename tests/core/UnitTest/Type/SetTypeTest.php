<?php
namespace Webbhuset\Bifrost\Core\Test\UnitTest\Type;
use Webbhuset\Bifrost\Core\Type as Core;

class SetTypeTest
{
    public static function __constructTest($test)
    {
        $params = [
            'type' =>  new Core\IntType(),
        ];
        $test->testThatArgs($params)
            ->notThrows('Exception');

        $params = ['type' => new \stdClass];
        $test->testThatArgs($params)
            ->throws('Webbhuset\Bifrost\Core\BifrostException');
            
        $params = [];
        $test->testThatArgs($params)
            ->throws('Webbhuset\Bifrost\Core\BifrostException');
    }

    public static function diffTest($test)
    {
        $params = [
            'type' =>  new Core\IntType(),
        ];
        $test->newInstance($params);

        /* Test that two equal sets returns empty diff */
        $old = [11, 12, 13, 14, 15];
        $new = [11, 12, 13, 14, 15];
        $expected = [
            '+' => [],
            '-' => [],
        ];
        $test->testThatArgs($old, $new)->returnsValue($expected);

        /* Test that two equal sets returns empty diff */
        $old = [11, 12, 13, 14, 15];
        $new = [15, 14, 13, 12, 11];
        $expected = [
            '+' => [],
            '-' => [],
        ];
        $test->testThatArgs($old, $new)->returnsValue($expected);

        /* Test added element  */
        $old = [11, 12, 13, 14];
        $new = [15, 14, 13, 12, 11];
        $expected = [
            '+' => [15],
            '-' => [],
        ];
        $test->testThatArgs($old, $new)->returnsValue($expected);

        /* Test removed element  */
        $old = [11, 12, 13, 14, 15];
        $new = [15, 14, 13, 12];
        $expected = [
            '+' => [],
            '-' => [11],
        ];
        $test->testThatArgs($old, $new)->returnsValue($expected);


    }

    public static function isEqualTest($test)
    {
        $params = [
            'type' =>  new Core\IntType(),
        ];
        $test->newInstance($params)
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

        $params = [
            'type' =>  new Core\StringType(),
        ];
        $test->newInstance($params)
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
        $params = [
            'type' =>  new Core\StringType(),
        ];

        $test->newInstance($params)
            ->testThatArgs(['abc','bbb','edf'])->returnsValue(false)
            ->testThatArgs([])->returnsValue(false)
            ->testThatArgs(['abc', 123, 'edf'])->notReturnsValue(false)
            ->testThatArgs(['abc', false])->notReturnsValue(false);

        $params = [
            'type' =>  new Core\IntType(),
            'min_size' => 2,
            'max_size' => 4,
        ];

        $test->newInstance($params)
            ->testThatArgs([1, 2, 3, 4])->returnsValue(false)
            ->testThatArgs([66, 77])->returnsValue(false)
            ->testThatArgs([1])->notReturnsValue(false)
            ->testThatArgs([1, 2, 3, 4, 5])->notReturnsValue(false);

    }

    public static function castTest($test)
    {
        $params = [
            'type' =>  new Core\IntType(),
        ];

        $test->newInstance($params)
            ->testThatArgs([1, 2, 33.0, '4'])->returnsValue([1, 2, 33, 4]);
    }
}
