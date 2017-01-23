<?php
namespace Webbhuset\Bifrost\Core\Test\UnitTest\Utils\Processor;
use Webbhuset\Bifrost\Core\Utils\Logger\NullLogger;
use Webbhuset\Bifrost\Core\Utils\Writer\Mock\Collector;

class BatcherTest
{

    public static function __constructTest($test)
    {
        $params = [
            'batch_size'   => 4,
        ];
        $nullLogger    = new NullLogger;
        $mockProcessor = [new Collector];

        $test
            ->testThatArgs($nullLogger, $mockProcessor, $params)
            ->notThrows('Exception');

        $params = ['batch_size'   => 'apa'];
        $test
            ->testThatArgs($nullLogger, $mockProcessor, $params)
            ->throws('Webbhuset\Bifrost\Core\BifrostException');
    }

    public static function processNextTest($test)
    {
        $nullLogger    = new NullLogger;
        $mockProcessor = [new Collector];

        $params = [
            'batch_size'   => 4,
        ];
        $indata = [
            [ 'name' => 'apa' ]
        ];
        $test->newInstance($nullLogger, $mockProcessor, $params)
            ->testThatArgs($indata)->returnsNull()
            ->testThatArgs($indata)->returnsNull()
            ->testThatArgs($indata)->returnsNull()
            ->testThatArgs($indata)->returnsNull()
            ->testThatArgs($indata)->returnsNull()
            ->testThatArgs($indata)->returnsNull()
            ->testThatArgs($indata)->returnsNull()
            ->testThatArgs($indata)->returnsNull();
    }
}
