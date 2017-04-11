<?php
namespace Webbhuset\Bifrost\Test\UnitTest\Utils\Processor;
use Webbhuset\Bifrost\Utils\Logger\NullLogger;
use Webbhuset\Bifrost\Utils\Writer\Mock\Collector;

class StatementExecuterTest
{

    public static function __constructTest($test)
    {
        $logger    = new NullLogger;
        $mockProcessor = [new Collector];
        $test
            ->testThatArgs($logger, $mockProcessor, [])->throws('Webbhuset\\Bifrost\\Core\\BifrostException')
            ->testThatArgs($logger, $mockProcessor, [
                'connection' => 'apa',
                'statement' => 'fisk',
            ])
            ->throws('Webbhuset\\Bifrost\\Core\\BifrostException');
    }

    public static function processNextTest($test)
    {

    }
}
