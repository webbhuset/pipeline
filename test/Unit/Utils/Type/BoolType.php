<?php
namespace Webbhuset\Bifrost\Test\Unit\Utils\Type;
use Webbhuset\Bifrost\Core\Utils\Type as Core;

class BoolType extends \Webbhuset\Bifrost\Test\TestAbstract
    implements \Webbhuset\Bifrost\Test\TestInterface
{


    protected function isEqualTest()
    {
        $this->newInstance()
            ->testThatArgs(true, true)->returns(true)
            ->testThatArgs(false, false)->returns(true)
            ->testThatArgs(true, false)->returns(false);
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
