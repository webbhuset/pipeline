<?php
namespace Webbhuset\Bifrost\Core\Test\UnitTest\Utils\Processor;
use Webbhuset\Bifrost\Core\Utils\Logger\NullLogger;
use Webbhuset\Bifrost\Core\Utils\Writer\Mock\Collector;
use Webbhuset\Bifrost\Core\Utils\Processor\Filler\Backend\DefaultValues;

class FillerTest
{
    public static function __constructTest($test)
    {
        $nullLogger    = new NullLogger;
        $mockProcessor = [new Collector];
        $backendParams = [
            'default_values' => ['price' => 123],
        ];
        $params = [
            'key_specification' => ['price'],
            'backend'           => new DefaultValues($backendParams),
        ];
        $test->testThatArgs($nullLogger, $mockProcessor, $params)->notThrows('Exception');

        $params = [];
        $test
            ->testThatArgs($nullLogger, $mockProcessor, $params)
            ->throws('Webbhuset\Bifrost\Core\BifrostException');
    }

    public static function processNextTest($test)
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
        $mockProcessor = [new Collector];
        $indata = [
            [
                'price' => [
                    'test' => [
                        'NOK' => 53,
                    ]
                ],
            ]
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
            'key_specification' => ['price'],
            'backend'           => $backend
        ];
        $test->newInstance($nullLogger, $mockProcessor, $params)
            ->testThatArgs($indata)->returnsNull();
    }
}
