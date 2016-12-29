<?php
namespace Webbhuset\Bifrost\Test\Unit\Utils\Processor;
use Webbhuset\Bifrost\Core\Utils\Logger\NullLogger;
use Webbhuset\Bifrost\Core\Utils\Processor\Mock;

class Batcher extends \Webbhuset\Bifrost\Test\TestAbstract
{
    public function __constructTest()
    {
    }

    public function initTest()
    {
    }

    public function countTest()
    {
    }

    public function processNextTest()
    {
        $nullLogger    = new NullLogger;
        $mockProcessor = new Mock;

        $params = [
            'batch_size'   => 4,
        ];
        $indata = [
            [ 'name' => 'apa' ]
        ];
        $this->newInstance($nullLogger, $mockProcessor, $params)
            ->testThatArgs($indata)->returns(null)
            ->testThatArgs($indata)->returns(null)
            ->testThatArgs($indata)->returns(null)
            ->testThatArgs($indata)->returns(null)
            ->testThatArgs($indata)->returns(null)
            ->testThatArgs($indata)->returns(null)
            ->testThatArgs($indata)->returns(null)
            ->testThatArgs($indata)->returns(null);

    }

    public function finalizeTest()
    {
    }
}
