<?php
namespace Webbhuset\Bifrost\Test\Unit\Utils\Processor;
use Webbhuset\Bifrost\Core\Utils\Logger\NullLogger;
use Webbhuset\Bifrost\Core\Utils\Processor\Mock;

class Filler extends \Webbhuset\Bifrost\Test\TestAbstract
    implements \Webbhuset\Bifrost\Test\TestInterface
{

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
        $indata = [
            'price' => [
                'test' => [
                    'NOK' => 53,
                ]
            ],
        ];
        $defaults = [
            'price' => [
                'test' => [
                    'EUR' => 132,
                    'SEK' => [],
                    'NOK' => 83,
                ]
            ],
        ];
        $expectedOutput = [
            'price' => [
                'test' => [
                    'NOK' => 53,
                    'EUR' => 132,
                    'SEK' => [],
                ]
            ],
        ];
        $params = [
            'fill_values' => $defaults
        ];
        $this->newInstance($nullLogger, $mockProcessor, $params)
            ->testThatArgs($indata)->returns(Null);
    }

    public function finalizeTest()
    {
    }

    public function returnApa()
    {
        return 'Apa';
    }
}
