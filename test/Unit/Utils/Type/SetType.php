<?php
namespace Webbhuset\Bifrost\Test\Unit\Utils\Type;
use Webbhuset\Bifrost\Core\Utils\Type as Core;

class SetType extends \Webbhuset\Bifrost\Test\TestAbstract
    implements \Webbhuset\Bifrost\Test\TestInterface
{

    protected function diffTest()
    {
        $params = [
            'type' =>  new Core\IntType(),
        ];
        $this->newInstance($params);

        /* Test that two equal sets returns empty diff */
        $old = [11, 12, 13, 14, 15];
        $new = [11, 12, 13, 14, 15];
        $expected = [
            '+' => [],
            '-' => [],
        ];
        $this->testThatArgs($old, $new)->returns($expected);

        /* Test that two equal sets returns empty diff */
        $old = [11, 12, 13, 14, 15];
        $new = [15, 14, 13, 12, 11];
        $expected = [
            '+' => [],
            '-' => [],
        ];
        $this->testThatArgs($old, $new)->returns($expected);

        /* Test added element  */
        $old = [11, 12, 13, 14];
        $new = [15, 14, 13, 12, 11];
        $expected = [
            '+' => [15],
            '-' => [],
        ];
        $this->testThatArgs($old, $new)->returns($expected);

        /* Test removed element  */
        $old = [11, 12, 13, 14, 15];
        $new = [15, 14, 13, 12];
        $expected = [
            '+' => [],
            '-' => [11],
        ];
        $this->testThatArgs($old, $new)->returns($expected);


    }

    protected function isEqualTest()
    {
        $params = [
            'type' =>  new Core\IntType(),
        ];
        $this->newInstance($params)
            ->testThatArgs(
                [11, 12, 13, 14, 15],
                [11, 12, 13, 14, 15]
            )
            ->returns(true)
            ->testThatArgs(
                [11, 12, 13, 14, 15],
                [15, 14, 13, 12, 11]
            )
            ->returns(true)
            ->testThatArgs(
                [11, 12, 13, 14, 15],
                [10, 12, 13, 14, 15]
            )
            ->returns(false)
            ->testThatArgs(
                [10, 12],
                [10, 12, 13, 14, 15]
            )
            ->returns(false);

        $params = [
            'type' =>  new Core\StringType(),
        ];
        $this->newInstance($params)
            ->testThatArgs(
                ['abc','bbb','edf'],
                ['abc','bbb','edf']
            )
            ->returns(true)
            ->testThatArgs(
                ['abc','bbb','edf'],
                ['bbb','abc','edf']
            )
            ->returns(true)
            ->testThatArgs(
                ['bbb','edf'],
                ['bbb','abc','edf']
            )
            ->returns(false);

    }

    protected function getErrorsTest()
    {
        $params = [
            'type' =>  new Core\StringType(),
        ];

        $this->newInstance($params)
            ->testThatArgs(['abc','bbb','edf'])->returns(false)
            ->testThatArgs([])->returns(false)
            ->testThatArgs(['abc', 123, 'edf'])->notReturns(false)
            ->testThatArgs(['abc', false])->notReturns(false);

        $params = [
            'type' =>  new Core\IntType(),
            'min_size' => 2,
            'max_size' => 4,
        ];

        $this->newInstance($params)
            ->testThatArgs([1, 2, 3, 4])->returns(false)
            ->testThatArgs([66, 77])->returns(false)
            ->testThatArgs([1])->notReturns(false)
            ->testThatArgs([1, 2, 3, 4, 5])->notReturns(false);

    }

    protected function castTest()
    {
        $params = [
            'type' =>  new Core\IntType(),
        ];

        $this->newInstance($params)
            ->testThatArgs([1, 2, 33.0, '4'])->returns([1, 2, 33, 4]);
    }
}
