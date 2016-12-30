<?php
namespace Webbhuset\Bifrost\Test\Unit\Utils\Processor;
use Webbhuset\Bifrost\Core\Utils\Logger\NullLogger;
use Webbhuset\Bifrost\Core\Utils\Processor\Mock;
use Webbhuset\Bifrost\Core\Utils\ValueConverter\StringToInt;
use Webbhuset\Bifrost\Core\Utils\ValueConverter\StringToFloat;

class Converter extends \Webbhuset\Bifrost\Test\TestAbstract
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
        $fields = [
            'price' => [
                'EUR' => new StringToFloat,
                'SEK' => new StringToFloat,
                'NOK' => new StringToFloat,
            ],
            'qty'  => new StringToInt,
            'name' => null
        ];
        $params = [
            'fields' => $fields,
        ];
        $nullLogger    = new NullLogger;
        $mockProcessor = new Mock;
        $this->newInstance($nullLogger, $mockProcessor, $params);

        $indata = [
            [
                'price' => [
                    'EUR' => '2.67',
                    'SEK' => '6',
                    'NOK' => '8.8945',
                ],
                'qty'  => '56',
                'name' => 'test'
            ],
            [
                'price' => [
                    'EUR' => '0.67',
                    'SEK' => '67',
                    'NOK' => '8.8945',
                ],
                'qty'  => '56',
                'name' => 'test'
            ]
        ];
        $this->testThatArgs($indata)->returns(Null);
    }

    public function finalizeTest()
    {
    }
}
