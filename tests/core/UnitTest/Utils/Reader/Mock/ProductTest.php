<?php
namespace Webbhuset\Bifrost\Core\Test\UnitTest\Utils\Reader\Mock;
use Webbhuset\Bifrost\Core\Utils\Logger\NullLogger;
use Webbhuset\Bifrost\Core\Utils\Processor\Mock;

class ProductTest
{

    public static function getEntityCountTest($test)
    {
        $nullLogger    = new NullLogger;
        $mockProcessor = [new Mock];
        $params = [
            'seed'           => 'apa',
            'no_of_entities' => 2,
        ];
        $test->newInstance($nullLogger, $mockProcessor, $params)
            ->testThatArgs()->returnsValue(2);

        $params = [
            'seed'           => 'apa',
            'no_of_entities' => 5,
        ];
        $test->newInstance($nullLogger, $mockProcessor, $params)
            ->testThatArgs()->returnsValue(5);
    }

    public static function processNextTest($test)
    {
        $nullLogger    = new NullLogger;
        $mockProcessor = [new Mock];
        $params = [
            'seed'           => 'apa',
            'no_of_entities' => 2,
        ];
        $test->newInstance($nullLogger, $mockProcessor, $params)
            ->testThatArgs()->returnsValue(true)
            ->testThatArgs()->returnsValue(true)
            ->testThatArgs()->returnsValue(false);
    }

    public static function finalizeTest()
    {
    }

    public static function initTest()
    {
    }

    public static function __constructTest()
    {
    }
}
