<?php
namespace Webbhuset\Bifrost\Core\Test\UnitTest\Utils\Processor;

use Webbhuset\Bifrost\Core\Utils\Processor\ProcessorInterface;
use Webbhuset\Bifrost\Core\Utils\Processor\AbstractProcessor;
use Webbhuset\Bifrost\Core\Utils\Logger\NullLogger;
use Webbhuset\Bifrost\Core\Utils\Writer\Mock\Collector;


class AbstractProcessorTest extends AbstractProcessor
{

    public static function __constructTest($test)
    {
        $logger     = new NullLogger;
        $processor  = new Collector;
        $params     = [];

        $test->testThatArgs($logger, ['asdf'], $params)
            ->throws('Webbhuset\\Bifrost\\Core\\BifrostException');

        $test->testThatArgs($logger, [$processor], $params)
            ->notThrows('Exception');
    }

    public static function initTest($test)
    {
        $logger     = new NullLogger;
        $processors = [
            new TestProcessor,
            new TestProcessor,
            new TestProcessor,
        ];

        /**
         * @testCase Test that all processors recieve init args
         */
        $test->newInstance($logger, $processors, [])
            ->testThatArgs('apa')->assertCallback(function($returnValue, $instance, $exception) {
                foreach ($instance->getNextSteps() as $processor) {
                    if ($processor->args !== 'apa') {
                        return 'Processor did not recieve init';
                    }
                }
            });

    }

    public static function processNextTest($test)
    {
        $logger     = new NullLogger;
        $processors = [
            new TestProcessor,
            new TestProcessor,
            new TestProcessor,
        ];

        /**
         * @testCase Test that all processors recieve init args
         */
        $test->newInstance($logger, $processors, [])
            ->testThatArgs([1, 2])->assertCallback(function($returnValue, $instance, $exception) {
                foreach ($instance->getNextSteps() as $processor) {
                    if ($processor->data !== [2, 3]) {
                        return 'Processor did not recieve processNext';
                    }
                }
            });
    }

    public static function finalizeTest($test)
    {
        $logger     = new NullLogger;
        $processors = [
            new TestProcessor,
            new TestProcessor,
            new TestProcessor,
        ];

        /**
         * @testCase Test that all processors recieve init args
         */
        $test->newInstance($logger, $processors, [])
            ->testThatArgs(true)->assertCallback(function($returnValue, $instance, $exception) {
                foreach ($instance->getNextSteps() as $processor) {
                    if ($processor->onlyForCount !== true) {
                        return 'Processor did not recieve params';
                    }
                }
            })
            ->testThatArgs(false)->assertCallback(function($returnValue, $instance, $exception) {
                foreach ($instance->getNextSteps() as $processor) {
                    if ($processor->onlyForCount !== false) {
                        return 'Processor did not recieve params';
                    }
                }
            });
    }

    public static function countTest($test)
    {
    }

    public static function getNextStepsTest($test)
    {
    }

    protected function processData($data)
    {
        return $data + 1;
    }
}


class TestProcessor implements ProcessorInterface
{
    public $args;
    public $data;
    public $onlyForCount;

    public function init($args)
    {
        $this->args = $args;
    }
    public function processNext($data, $onlyForCount)
    {
        $this->data = $data;
    }
    public function finalize($onlyForCount)
    {
        $this->onlyForCount = $onlyForCount;
    }
    public function count()
    {
    }
    public function getNextSteps()
    {
    }
}
