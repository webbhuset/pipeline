<?php

namespace Webbhuset\Whaskell\Test\UnitTest\IO\File\Write;

class LineTest
{
    public static function __constructTest($test)
    {

    }

    public static function __invokeTest($test)
    {
        $testFile = __DIR__ . '/example-line.txt';

        if (file_exists($testFile)) {
            unlink($testFile);
        }

        /**
         * @testCase Write lines to file.
         */
        $lines = [
            'Line 1',
            'Line 2',
            'Line 3',
        ];
        $test->newInstance($testFile)
            ->testThatArgs($lines)
            ->returnsGenerator()
            ->returnsStrictValue($lines)
            ->assertCallback(function() use ($testFile, $lines) {
                if (!file_exists($testFile)) {
                    return "Expected '{$testFile}' to exist after writing";
                }

                $content    = file_get_contents($testFile);
                $allLines   = implode("\n", $lines) . "\n";
                if ($content != $allLines) {
                    return "Expected content in file to match input.";
                }
            });

        if (file_exists($testFile)) {
            unlink($testFile);
        }
    }
}
