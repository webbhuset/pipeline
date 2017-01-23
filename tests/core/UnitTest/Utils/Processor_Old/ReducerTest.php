<?php

namespace Webbhuset\Bifrost\Core\Test\UnitTest\Utils\Processor;
use Webbhuset\Bifrost\Core\Utils\Logger\NullLogger;
use Webbhuset\Bifrost\Core\Utils\Writer\Mock\Collector;

class ReducerTest
{
    public static function __constructTest($test)
    {
        $nullLogger    = new NullLogger;
        $mockProcessor = [new Collector];
        $params        = [];
        $test
            ->testThatArgs($nullLogger, $mockProcessor, [])
            ->throws('Webbhuset\Bifrost\Core\BifrostException');

        $test
            ->testThatArgs($nullLogger, $mockProcessor, ['callback' => __METHOD__])
            ->notThrows('Webbhuset\Bifrost\Core\BifrostException');
    }

    public static function processNextTest($test)
    {

    }

    public static function processDataTest($test)
    {
        $nullLogger    = new NullLogger;
        $mockProcessor = [new Collector];

        $numbers = [1, 2, 3];
        $callback = function($acumulator, $item) {
            return $acumulator + $item;
        };

        $test->newInstance($nullLogger, $mockProcessor, ['callback' => $callback])
            ->testThatArgs($numbers)->returnsValue(6);

        $callback = function($accumulator, $item) {
             $accumulator[] = $item['sku'];

             return $accumulator;
        };
        $items = [
            [
                'sku' => 'ABC',
                'name' => 'test',
            ],
            [
                'sku' => 'DEF',
                'name' => 'test',
            ],
            [
                'sku' => 'GHI',
                'name' => 'test',
            ],
        ];
        $test->newInstance($nullLogger, $mockProcessor, [
                'callback' => $callback,
                'initial' => [],
            ])
            ->testThatArgs($items)->returnsStrictValue(['ABC', 'DEF', 'GHI']);
    }
}
