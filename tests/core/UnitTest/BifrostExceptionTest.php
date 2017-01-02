<?php
namespace Webbhuset\Bifrost\Core\Test\UnitTest;

class BifrostExceptionTest
{
    public static function __constructTest()
    {
    }

    public static function getPrefixTest($test)
    {
        $test->newInstance('test')->testThatArgs()->returnsValue('Error');
    }
}
