<?php
namespace Webbhuset\Bifrost\Test\Unit\Utils\Type;
use Webbhuset\Bifrost\Core\Utils\Type as Core;

class IntType extends \Webbhuset\Bifrost\Test\TestAbstract
    implements \Webbhuset\Bifrost\Test\TestInterface
{


    protected function isEqualTest()
    {
        $this->newInstance()
            ->testThatArgs(5123, 5123)->returns(true)
            ->testThatArgs(-45123, -45123)->returns(true)
            ->testThatArgs(0, 0)->returns(true);
            /*->testThatArgs('0', 0)->returns(false)
            ->testThatArgs(null, 0)->returns(false)
            ->testThatArgs(0, [])->returns(false)
            ->testThatArgs(5123, -5123)->returns(false);*/
    }

    protected function getErrorsTest()
    {
        $this->newInstance()
            ->testThatArgs(231)->returns(false)
            ->testThatArgs(null)->returns(false)
            ->testThatArgs('12')->notReturns(false)
            ->testThatArgs(12.123)->notReturns(false)
            ->testThatArgs([12])->notReturns(false);

        $this->newInstance(['required' => true])
            ->testThatArgs(9867)->returns(false)
            ->testThatArgs(null)->notReturns(false)
            ->testThatArgs('9867')->notReturns(false)
            ->testThatArgs(12.123)->notReturns(false)
            ->testThatArgs([12])->notReturns(false);

        $this->newInstance(['min_value' => 4])
            ->testThatArgs(-5)->notReturns(false)
            ->testThatArgs(5)->returns(false)
            ->testThatArgs(null)->returns(false);

        $this->newInstance(['max_value' => 40])
            ->testThatArgs(5)->returns(false)
            ->testThatArgs(55)->notReturns(false)
            ->testThatArgs(null)->returns(false);
    }

    protected function castTest()
    {
        $this->newInstance()
            ->testThatArgs(12)->returns(12)
            ->testThatArgs(null)->returns(null)
            ->testThatArgs('123')->returns(123)
            ->testThatArgs(12.0)->returns(12);


        $this->newInstance(['required' => true])
            ->testThatArgs(12)->returns(12)
            ->testThatArgs(null)->returns(null)
            ->testThatArgs('123')->returns(123)
            ->testThatArgs(12.0)->returns(12);
    }
}
