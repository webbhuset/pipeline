<?php
namespace Webbhuset\Bifrost\Core\Test\UnitTest\Component\Dev\Mock;

use Webbhuset\Bifrost\Core\Component\Dev\Mock;

class ProductTest
{
    public static function __constructTest($test)
    {
    }

    public static function processTest($test)
    {
        $processor = new Mock\Product('seed');
        $items = iterator_to_array($processor->process(30));
        $test->newInstance('seed')
            ->testThatArgs(30)
            ->returnsGenerator()
            ->returnsStrictValue($items);
    }
}
