<?php
namespace Webbhuset\Bifrost\Test\UnitTest;

use Webbhuset\Bifrost as Bifrost;

class JobTest
{
    public static function __constructTest($test)
    {
        $test->testThatArgs(self::getTestComponent(), 1)
            ->notThrows('Exception');
    }

    public static function processNextTest($test)
    {
        /**
         * @testCase The function returns true when there's more to process.
         */
        $test->newInstance(self::getTestComponent(), 3)
            ->testThatArgs()
            ->returnsTrue();

        /**
         * @testCase The function returns false when done.
         */
        $test->newInstance(self::getTestComponent(), 3)
            ->testThatArgs()
            ->testThatArgs()
            ->testThatArgs()
            ->returnsFalse();
    }

    public static function isDoneTest($test)
    {
        /**
         * @testCase The function returns false when there's more to process.
         */
        $test->newInstance(self::getTestComponent(), 3)
            ->assertCallback(function($returnValue, $instance, $exception) {
                $instance->processNext();
                $value = $instance->isDone();

                if ($value !== false) {
                    return 'Expected isDone() to return false when processing is not done.';
                }
            });

        /**
         * @testCase The function returns true when done.
         */
        $test->newInstance(self::getTestComponent(), 3)
            ->assertCallback(function($returnValue, $instance, $exception) {
                $instance->processNext();
                $instance->processNext();
                $instance->processNext();
                $value = $instance->isDone();

                if ($value !== true) {
                    return 'Expected isDone() to return true after processing is done.';
                }
            });
    }

    protected static function getTestComponent()
    {
        return new Bifrost\Component\Read\Mock\Product('seed');
    }
}
