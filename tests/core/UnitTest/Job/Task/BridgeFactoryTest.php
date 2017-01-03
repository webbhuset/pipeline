<?php
namespace  Webbhuset\Bifrost\Core\Test\UnitTest\Job\Task;
use Webbhuset\Bifrost\Core\Job\Task\BridgeFactory;
use Webbhuset\Bifrost\Core\Utils\Processor\ProcessorFactory;
use Webbhuset\Bifrost\Core\Utils\Reader\ReaderFactory;
use Webbhuset\Bifrost\Core\Utils\Logger\NullLogger;

class BridgeFactoryTest extends BridgeFactory
{

    protected $bridgeSpecification =  <<<BRIDGE
mockProductReader
    ->mockProcessor1
    ->mockProcessor2->mockProcessor2
        ->mockProcessor1
        ->mockProcessor2->mockProcessor2
BRIDGE;

    public function mockProductReader()
    {
        $params = [
            'seed'           => 'apa',
            'no_of_entities' => 2,
        ];
        return new ReaderFactory('Webbhuset\Bifrost\Core\Utils\Reader\Mock\Product', $params);
    }

    public function mockProcessor1()
    {
        return new ProcessorFactory('Webbhuset\Bifrost\Core\Utils\Processor\Mock', []);
    }
    public function mockProcessor2()
    {
        return new ProcessorFactory('Webbhuset\Bifrost\Core\Utils\Processor\Mock', []);
    }

    public static function createTest($test)
    {
        $logger = new NullLogger;
        $test->newInstance($logger)
            ->testThatArgs()->returnsInstanceOf('Webbhuset\Bifrost\Core\Utils\Reader\Mock\Product');
    }
}
