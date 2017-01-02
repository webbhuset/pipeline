<?php
namespace Webbhuset\Bifrost\Test\Unit\Utils\Type;
use Webbhuset\Bifrost\Core\Utils\Type as Core;

class FloatType extends \Webbhuset\Bifrost\Test\TestAbstract
    implements \Webbhuset\Bifrost\Test\TestInterface
{

    protected function isEqualTest()
    {
        $this->newInstance()
            ->testThatArgs(514.0, 514.0)->returns(true)
            ->testThatArgs(-45514.0, -45514.0)->returns(true)
            ->testThatArgs(614.002, 614.002)->returns(true)
            ->testThatArgs(61.00000000002, 61.00000000001)->returns(true)
            ->testThatArgs(0.000, 0.000000)->returns(true);
            /*->testThatArgs('0', 0.0)->returns(false)
            ->testThatArgs(null, 0.0)->returns(false)
            ->testThatArgs([], 0.0)->throws();*/
    }

    protected function getErrorsTest()
    {
        $this->newInstance()
            ->testThatArgs(231.123)->returns(false)
            ->testThatArgs(null)->returns(false)
            ->testThatArgs('124.123')->notReturns(false)
            ->testThatArgs(12)->notReturns(false)
            ->testThatArgs([12])->notReturns(false);

        $this->newInstance(['required' => true])
            ->testThatArgs(9867.00)->returns(false)
            ->testThatArgs(null)->notReturns(false)
            ->testThatArgs('9867.123')->notReturns(false)
            ->testThatArgs(123)->notReturns(false)
            ->testThatArgs([12])->notReturns(false);

        $this->newInstance(['min_value' => -4.5])
            ->testThatArgs(-4.6)->notReturns(false)
            ->testThatArgs(5.5)->returns(false)
            ->testThatArgs(null)->returns(false);

        $this->newInstance(['max_value' => 40.5])
            ->testThatArgs(5.127)->returns(false)
            ->testThatArgs(40.556)->notReturns(false)
            ->testThatArgs(null)->returns(false);
    }

    protected function castTest()
    {
        $this->newInstance()
            ->testThatArgs(12.36)->returns(12.36)
            ->testThatArgs(null)->returns(null)
            ->testThatArgs('123')->returns(123.0)
            ->testThatArgs(12)->returns(12.0);

        $this->newInstance(['required' => true])
            ->testThatArgs(12.36)->returns(12.36)
            ->testThatArgs(null)->returns(null)
            ->testThatArgs('123')->returns(123.0)
            ->testThatArgs(12)->returns(12.0);
    }
}
