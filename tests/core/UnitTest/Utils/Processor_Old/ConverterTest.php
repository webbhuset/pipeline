<?php
namespace Webbhuset\Bifrost\Test\UnitTest\Utils\Processor;
use Webbhuset\Bifrost\Utils\Logger\NullLogger;
use Webbhuset\Bifrost\Utils\Writer\Mock\Collector;
use Webbhuset\Bifrost\Utils\ValueConverter\StringToInt;
use Webbhuset\Bifrost\Utils\ValueConverter\StringToFloat;

class ConverterTest
{
    public static function __constructTest($test)
    {
    }

    public static function processDataTest($test)
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
        $mockProcessor = [new Collector];
        $test->newInstance($nullLogger, $mockProcessor, $params);

        $indata = [
            'price' => [
                'EUR' => '2.67',
                'SEK' => '6',
                'NOK' => '8.8945',
            ],
            'qty'  => '56',
            'name' => 'test'
        ];
        $expected = [
            'price' => [
                'EUR' => 2.67,
                'SEK' => 6.0,
                'NOK' => 8.8945,
            ],
            'qty'  => 56,
            'name' => 'test'
        ];

        $test->testThatArgs($indata)->returnsStrictValue($expected);
    }

    public static function processNextTest($test)
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
        $mockProcessor = [new Collector];
        $test->newInstance($nullLogger, $mockProcessor, $params);

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
        $test->testThatArgs($indata)->returnsNull();
    }
}
