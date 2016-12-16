<?php
namespace Webbhuset\Bifrost\Test\Unit\Utils\Type;
use Webbhuset\Bifrost\Core\Utils\Type as Core;

class StringType extends \Webbhuset\Bifrost\Test\TestAbstract
    implements \Webbhuset\Bifrost\Test\TestInterface
{
    protected function isEqualTest()
    {
        $this->newInstance()
            ->testThatArgs('apa123', 'apa123')->returns(true)
            ->testThatArgs('', '')->returns(true)
            ->testThatArgs('apa123', 'apa')->returns(false)
            ->testThatArgs('apa', 'apa123')->returns(false);
            /*->testThatArgs('', null)->returns(false)
            ->testThatArgs(null, '')->returns(false)
            ->testThatArgs(0, '')->returns(false)
            ->testThatArgs(0, [])->returns(false);*/
    }

    protected function getErrorsTest()
    {
        $this->newInstance()
            ->testThatArgs('apa123')->returns(false)
            ->testThatArgs(null)->returns(false)
            ->testThatArgs(12)->notReturns(false)
            ->testThatArgs(12.123)->notReturns(false)
            ->testThatArgs(['12'])->notReturns(false);

        $this->newInstance(['required' => true])
            ->testThatArgs('apa123')->returns(false)
            ->testThatArgs(null)->notReturns(false)
            ->testThatArgs(12)->notReturns(false)
            ->testThatArgs(12.123)->notReturns(false)
            ->testThatArgs(['12'])->notReturns(false);

        $this->newInstance(['min_length' => 4])
            ->testThatArgs('apa')->notReturns(false)
            ->testThatArgs('apa123')->returns(false)
            ->testThatArgs(null)->returns(false);

        $this->newInstance(['max_length' => 4])
            ->testThatArgs('apa')->returns(false)
            ->testThatArgs('apa123')->notReturns(false)
            ->testThatArgs(null)->returns(false);
    }

    protected function castTest()
    {
        $this->newInstance()
            ->testThatArgs('apa123')->returns('apa123')
            ->testThatArgs(null)->returns(null)
            ->testThatArgs(123)->returns('123')
            ->testThatArgs(12.335)->returns('12.335');

        $this->newInstance(['required' => true])
            ->testThatArgs('apa123')->returns('apa123')
            ->testThatArgs(null)->returns(null)
            ->testThatArgs(123)->returns('123')
            ->testThatArgs(12.335)->returns('12.335');
    }
}
