<?php

namespace Webbhuset\Bifrost\Core\Test\UnitTest\Component\File;

class MoveTest
{
    const TEST_DIR = '/tmp/bifrost-test';

    public static function __constructTest($test)
    {

    }

    public static function processTest($test)
    {
        mkdir(self::TEST_DIR);

        /**
         * @testCase Moves a.txt to b.txt.
         */
        $from           = self::TEST_DIR . '/a.txt';
        $to             = self::TEST_DIR . '/b.txt';
        $moveFunction   = function ($item) use ($to) {
            return $to;
        };
        touch($from);
        $test->newInstance($moveFunction)
            ->testThatArgs([$from])
            ->returnsGenerator()
            ->returnsValue([$to])
            ->assertCallback(function() use ($from, $to) {
                if (file_exists($from)) {
                    return "Expected {$from} to not exist (since it should have been moved).";
                }

                if (!file_exists($to)) {
                    return "Expected {$to} to exist (since it should have been moved).";
                }
            });
        unlink($to);

        /**
         * @testCase Copies a.txt to b.txt.
         */
        $from           = self::TEST_DIR . '/a.txt';
        $to             = self::TEST_DIR . '/b.txt';
        $moveFunction   = function ($item) use ($to) {
            return $to;
        };
        touch($from);
        $test->newInstance($moveFunction, ['copy' => true])
            ->testThatArgs([$from])
            ->returnsGenerator()
            ->returnsValue([$to])
            ->assertCallback(function() use ($from, $to) {
                if (!file_exists($from)) {
                    return "Expected {$from} to exist (since it should have been copied).";
                }

                if (!file_exists($to)) {
                    return "Expected {$to} to exist (since it should have been copied).";
                }
            });
        unlink($from);
        unlink($to);

        rmdir(self::TEST_DIR);
    }
}
