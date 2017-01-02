<?php
namespace Webbhuset\Bifrost\Core\Test\UnitTest\Utils\Type;

class BoolTypeTest
{
    public static function isEqualTest($test)
    {
        $test->newInstance()
            ->testThatArgs(true, true)->returnsValue(true)
            ->testThatArgs(false, false)->returnsValue(true)
            ->testThatArgs(true, false)->returnsValue(false);
    }

    protected function getErrorsTest()
    {
        $this->newInstance()
            ->testThatArgs(false)->returns(false)
            ->testThatArgs(true)->returns(false)
            ->testThatArgs(null)->returns(false)
            ->testThatArgs('false')->notReturns(false)
            ->testThatArgs('1')->notReturns(false)
            ->testThatArgs(1)->notReturns(false)
            ->testThatArgs([true])->notReturns(false);

        $this->newInstance(['required' => true])
            ->testThatArgs(false)->returns(false)
            ->testThatArgs(true)->returns(false)
            ->testThatArgs(null)->notReturns(false);

    }

    protected function castTest()
    {
        $this->newInstance()
            ->testThatArgs(1)->returns(true)
            ->testThatArgs(0)->returns(false)
            ->testThatArgs(null)->returns(null)
            ->testThatArgs('false')->returns(true)
            ->testThatArgs('')->returns(false);



        $this->newInstance(['required' => true])
            ->testThatArgs(1)->returns(true)
            ->testThatArgs(0)->returns(false)
            ->testThatArgs(null)->returns(null)
            ->testThatArgs('false')->returns(true)
            ->testThatArgs('')->returns(false);

    }
}
