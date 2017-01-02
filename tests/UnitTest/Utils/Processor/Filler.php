<?php
namespace Webbhuset\Bifrost\Test\Unit\Utils\Processor;
use Webbhuset\Bifrost\Core\Utils\Logger\NullLogger;
use Webbhuset\Bifrost\Core\Utils\Processor\Mock;
use Webbhuset\Bifrost\Core\Utils\Processor\Filler\Backend\DefaultValues;

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
        $defaults = [
            'price' => [
                'test' => [
                    'EUR' => 132,
                    'SEK' => [],
                    'NOK' => 83,
                ]
            ],
        ];
        $backendParams = [
            'default_values' => $defaults,
        ];
        $backend       = new DefaultValues($backendParams);
        $nullLogger    = new NullLogger;
        $mockProcessor = new Mock;
        $indata = [
            'price' => [
                'test' => [
                    'NOK' => 53,
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
            'backend' => $backend
        ];
        $this->newInstance($nullLogger, $mockProcessor, $params)
            ->testThatArgs($indata)->returns(Null);
    }

    public function finalizeTest()
    {
    }
}
