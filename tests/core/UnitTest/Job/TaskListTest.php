<?php
namespace Webbhuset\Bifrost\Test\UnitTest\Job;
use Webbhuset\Bifrost\Utils\Logger\NullLogger;
use Webbhuset\Bifrost\Utils\Reader\Mock\Product;

class TaskListTest
{
    public static function __constructTest($test)
    {
        $test->testThatArgs(['apa'])
            ->throws('Webbhuset\Bifrost\BifrostException');
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
