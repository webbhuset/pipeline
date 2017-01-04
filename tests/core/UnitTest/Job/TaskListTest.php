<?php
namespace Webbhuset\Bifrost\Core\Test\UnitTest\Job;
use Webbhuset\Bifrost\Core\Utils\Logger\NullLogger;
use Webbhuset\Bifrost\Core\Utils\Processor\Mock;
use Webbhuset\Bifrost\Core\Utils\Reader\Mock\Product;

class TaskListTest
{
    public static function __constructTest($test)
    {
        $test->testThatArgs(['apa'])
            ->throws('Webbhuset\Bifrost\Core\BifrostException');
    }

    public static function initTest()
    {
    }

    public static function processNextTest()
    {
    }

    public static function getCurrentTaskTest()
    {
    }

    public static function isDoneTest()
    {
    }

    public static function finalizeTest()
    {
    }
}
