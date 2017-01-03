<?php
namespace Webbhuset\Bifrost\Core\Test\UnitTest\Utils\Processor;
use Webbhuset\Bifrost\Core\Utils\Logger\NullLogger;
use Webbhuset\Bifrost\Core\Utils\Processor\Mock;
use Webbhuset\Bifrost\Core\Utils\Type as Type;

class EntityValidatorTest
{
    public static function __constructTest($test)
    {
        $nullLogger    = new NullLogger;
        $mockProcessor = [new Mock];
        $params = [
            'type' => new \stdClass
        ];
        $test->testThatArgs($nullLogger, $mockProcessor, $params)
            ->throws('Webbhuset\Bifrost\Core\BifrostException');

        $params = [];
        $test->testThatArgs($nullLogger, $mockProcessor, $params)
            ->throws('Webbhuset\Bifrost\Core\BifrostException');
    }

    public static function processDataTest($test)
    {
        $structParams = [
            'fields' => [
                'sku'   =>  new Type\StringType(),
                'price' =>  new Type\IntType(),
            ]
        ];
        $type          = new Type\StructType($structParams);
        $nullLogger    = new NullLogger;
        $mockProcessor = [new Mock];
        $params = [
            'type' => $type
        ];
        $test->newInstance($nullLogger, $mockProcessor, $params);

        $indata = [
            'sku'   => '123',
            'price' => 53,
        ];
        $test->testThatArgs($indata)->returnsValue($indata);

        $indata = [
            'sku'   => '123',
            'price' => '53',
        ];
        $test->testThatArgs($indata)->returnsNull();
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
        $mockProcessor = [new Mock];
        $indata = [
            [
                'sku'   => '123',
                'price' => 53,
            ],
            [
                'sku'   => 125,
                'price' => 53,
            ]
        ];

        $params = [
            'type' => $type
        ];
        $test->newInstance($nullLogger, $mockProcessor, $params)
            ->testThatArgs($indata)->returnsNull();
    }

    public static function finalizeTest()
    {
    }

    public static function initTest()
    {
    }

    public static function countTest()
    {
    }
}
