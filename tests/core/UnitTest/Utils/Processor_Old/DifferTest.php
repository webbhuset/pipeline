<?php
namespace Webbhuset\Bifrost\Test\UnitTest\Utils\Processor;
use Webbhuset\Bifrost\Utils\Logger\NullLogger;
use Webbhuset\Bifrost\Utils\Writer\Mock\Collector;
use Webbhuset\Bifrost\Utils\Type as Type;

class DifferTest
{
    public static function __constructTest($test)
    {
        $structParams = [
            'fields' => [
                'sku'   =>  new Type\StringType(),
                'price' =>  new Type\IntType(),
            ]
        ];
        $nullLogger    = new NullLogger;
        $mockProcessor = [new Collector];
        $params = [
            'type' => new Type\StructType($structParams),
        ];
        $test
            ->testThatArgs($nullLogger, $mockProcessor, $params)
            ->notThrows('Exception');

        $params = [];
        $test
            ->testThatArgs($nullLogger, $mockProcessor, $params)
            ->throws('Webbhuset\Bifrost\BifrostException');
    }

    public static function processDataTest($test)
    {
        $structParams = [
            'fields' => [
                'sku'   =>  new Type\StringType(),
                'price' =>  new Type\IntType(),
            ]
        ];
        $nullLogger    = new NullLogger;
        $mockProcessor = [new Collector];
        $params = [
            'type' => new Type\StructType($structParams),
        ];
        $test->newInstance($nullLogger, $mockProcessor, $params);

        $indata = [
            'old' => [
                    'sku'   => '123',
                    'price' => 53,
            ],
            'new' => [
                    'sku'   => '123',
                    'price' => 53,
            ],
        ];
        $expected = [];
        $test->testThatArgs($indata)->returnsValue($expected);

        $indata = [
            'old' => [
                    'sku'   => '123',
                    'price' => 53,
            ],
            'new' => [
                    'sku'   => '123',
                    'price' => 567,
            ],
        ];
        $expected = [
            'price' => [
                '+' => 567,
                '-' => 53
            ]
        ];
        $test->testThatArgs($indata)->returnsValue($expected);
    }

    public static function processNextTest($test)
    {
        $structParams = [
            'fields' => [
                'sku'   =>  new Type\StringType(),
                'price' =>  new Type\IntType(),
            ]
        ];
        $type          = new Type\StructType($structParams);
        $nullLogger    = new NullLogger;
        $mockProcessor = [new Collector];
        $indata = [
            [
                'old' => [
                        'sku'   => '123',
                        'price' => 53,
                ],
                'new' => [
                        'sku'   => '123',
                        'price' => 53,
                ],
            ],
            [
                'old' => [
                        'sku'   => '123',
                        'price' => 53,
                ],
                'new' => [
                        'sku'   => '123',
                        'price' => 567,
                ],
            ]
        ];

        $params = [
            'type' => $type
        ];
        $test->newInstance($nullLogger, $mockProcessor, $params)
            ->testThatArgs($indata)->returnsNull();
    }
}
