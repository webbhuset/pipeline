<?php
namespace Webbhuset\Bifrost\Core\Test\UnitTest\Job;
use Webbhuset\Bifrost\Core\Utils\Logger\NullLogger;
use Webbhuset\Bifrost\Core\Utils\Writer\Mock\Collector;
use Webbhuset\Bifrost\Core\Utils\Reader\Mock\Product;

class TaskTest
{
    public static function __constructTest($test)
    {
        $nullLogger    = new NullLogger;
        $mockProcessor = [new Collector];
        $params = [
            'seed'           => 'apa',
            'no_of_entities' => 2,
        ];
        $mockProductReader = new Product($nullLogger, $mockProcessor, $params);

        $test->testThatArgs($nullLogger, 'Test', $mockProductReader)
            ->notThrows('Exception');
    }

    public static function initTest()
    {
    }

    public static function processNextTest($test)
    {
        $nullLogger    = new NullLogger;
        $mockProcessor = [new Collector];
        $params = [
            'seed'           => 'apa',
            'no_of_entities' => 4,
        ];
        $mockProductReader = new Product($nullLogger, $mockProcessor, $params);

        $test->newInstance($nullLogger, 'Test', $mockProductReader)
            ->testThatArgs()->returnsValue(true)
            ->testThatArgs()->returnsValue(true)
            ->testThatArgs()->returnsValue(true)
            ->testThatArgs()->returnsValue(true)
            ->testThatArgs()->returnsValue(false);
    }

    public static function isDoneTest()
    {
    }

    public static function finalizeTest()
    {
    }

    public static function getLoggerTest()
    {
    }
}
