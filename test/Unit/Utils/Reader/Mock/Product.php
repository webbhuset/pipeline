<?php
namespace Webbhuset\Bifrost\Test\Unit\Utils\Reader\Mock;
use Webbhuset\Bifrost\Core\Utils\Logger\NullLogger;
use Webbhuset\Bifrost\Core\Utils\Processor\Mock;

class Product extends \Webbhuset\Bifrost\Test\TestAbstract
    implements \Webbhuset\Bifrost\Test\TestInterface
{

    public function getEntityCountTest()
    {
        $nullLogger    = new NullLogger;
        $mockProcessor = new Mock;
        $params = [
            'seed'           => 'apa',
            'no_of_entities' => 2,
        ];
        $this->newInstance($nullLogger, $mockProcessor, $params)
            ->testThatArgs()->returns(2);

        $params = [
            'seed'           => 'apa',
            'no_of_entities' => 5,
        ];
        $this->newInstance($nullLogger, $mockProcessor, $params)
            ->testThatArgs()->returns(5);
    }

    public function processNextTest()
    {
        $nullLogger    = new NullLogger;
        $mockProcessor = new Mock;
        $params = [
            'seed'           => 'apa',
            'no_of_entities' => 2,
        ];
        $this->newInstance($nullLogger, $mockProcessor, $params)
            ->testThatArgs()->returns(true)
            ->testThatArgs()->returns(true)
            ->testThatArgs()->returns(false);
    }

    public function finalizeTest()
    {
    }

    public function initTest()
    {
    }

    public function __constructTest()
    {
    }
}
