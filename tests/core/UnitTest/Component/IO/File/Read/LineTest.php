<?php

namespace Webbhuset\Bifrost\Core\Test\UnitTest\Component\IO\File\Read;

class LineTest
{
    public static function __constructTest($test)
    {

    }

    public static function processTest($test)
    {
        $testFile = __DIR__ . '/example-line.txt';

        /**
         * @testCase Read lines from file.
         */
        $test->newInstance()
            ->testThatArgs([$testFile])
            ->returnsGenerator()
            ->returnsStrictValue([
                'Line 1',
                'Line 2',
                'Line 3',
                'Line 5',
                'Line 6',
            ]);

        /**
         * @testCase Read lines from file, including empty lines.
         */
        $test->newInstance(['ignore_empty' => false])
            ->testThatArgs([$testFile])
            ->returnsGenerator()
            ->returnsStrictValue([
                'Line 1',
                'Line 2',
                'Line 3',
                '',
                'Line 5',
                'Line 6',
                '',
            ]);
    }
}
