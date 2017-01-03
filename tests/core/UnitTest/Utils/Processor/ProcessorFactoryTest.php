<?php
namespace Webbhuset\Bifrost\Core\Test\UnitTest\Utils\Processor;
use Webbhuset\Bifrost\Core\Utils\Logger\NullLogger;
use Webbhuset\Bifrost\Core\Utils\Processor\Mock;

class ProcessorFactoryTest
{
    public static function __constructTest($test)
    {
        $test->testThatArgs('Webbhuset\Bifrost\Core\Utils\Processor\Mock', [])
            ->notThrows('Exception');

        $test->testThatArgs('stdClass', [])
            ->throws('Webbhuset\Bifrost\Core\BifrostException');
    }

    public static function createTest($test)
    {
        $nullLogger = new NullLogger;
        $test->newInstance('Webbhuset\Bifrost\Core\Utils\Processor\Mock',[])
            ->testThatArgs($nullLogger, [])->returnsInstanceOf('Webbhuset\Bifrost\Core\Utils\Processor\Mock');
    }
}
