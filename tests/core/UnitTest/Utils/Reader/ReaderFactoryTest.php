<?php
namespace Webbhuset\Bifrost\Core\Test\UnitTest\Utils\Reader;
use Webbhuset\Bifrost\Core\Utils\Logger\NullLogger;
use Webbhuset\Bifrost\Core\Utils\Processor\Mock;

class ReaderFactoryTest
{
    public static function __constructTest($test)
    {
        $test->testThatArgs('Webbhuset\Bifrost\Core\Utils\Reader\Mock\Product', [])
            ->notThrows('Exception');

        $test->testThatArgs('stdClass', [])
            ->throws('Webbhuset\Bifrost\Core\BifrostException');
    }

    public static function createTest($test)
    {
        $params = [
            'seed'           => 'apa',
            'no_of_entities' => 2,
        ];
        $nullLogger = new NullLogger;
        $test->newInstance('Webbhuset\Bifrost\Core\Utils\Reader\Mock\Product', $params)
            ->testThatArgs($nullLogger, new Mock)->returnsInstanceOf('Webbhuset\Bifrost\Core\Utils\Reader\Mock\Product');
    }
}
