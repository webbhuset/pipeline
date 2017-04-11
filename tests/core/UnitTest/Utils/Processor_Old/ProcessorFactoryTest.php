<?php
namespace Webbhuset\Bifrost\Test\UnitTest\Utils\Processor;
use Webbhuset\Bifrost\Utils\Logger\NullLogger;


class ProcessorFactoryTest
{
    public static function __constructTest($test)
    {
        $test->testThatArgs('Webbhuset\Bifrost\Utils\Writer\Mock\Collector', [])
            ->notThrows('Exception');

        $test->testThatArgs('stdClass', [])
            ->throws('Webbhuset\Bifrost\BifrostException');
    }

    public static function createTest($test)
    {
        $nullLogger = new NullLogger;
        $test->newInstance('Webbhuset\Bifrost\Utils\Writer\Mock\Collector',[])
            ->testThatArgs($nullLogger, [])->returnsInstanceOf('Webbhuset\Bifrost\Utils\Writer\Mock\Collector');
    }
}
