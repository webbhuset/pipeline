<?php

namespace Webbhuset\Bifrost\Core\Test\UnitTest\Component\IO\File;

class MoveTest
{
    const TEST_DIR = '/tmp/bifrost-test';

    public static function __constructTest($test)
    {

    }

    public static function processTest($test)
    {
        if (!file_exists(self::TEST_DIR)) {
            mkdir(self::TEST_DIR);
        }

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
            ->returnsArray()
            ->assertCallback(function($value) use ($from, $to) {
                if ($value[0] !== $to) {
                    return "Expected yielded value to be '{$to}'";
                }

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
            ->returnsArray()
            ->assertCallback(function($value) use ($from, $to) {
                if ($value[0] !== $to) {
                    return "Expected yielded value to be '{$to}'";
                }

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
