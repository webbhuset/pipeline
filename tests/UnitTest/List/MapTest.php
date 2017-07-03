<?php

namespace Webbhuset\Bifrost\Test\UnitTest\Component\Iterable;

class MapTest
{
    public static function __constructTest($test)
    {
        $test
            ->testThatArgs(123)->throws('Webbhuset\\Bifrost\\Core\\BifrostException')
            ->testThatArgs(function(){})->throws('Webbhuset\\Bifrost\\Core\\BifrostException')
            ->testThatArgs(function($item){})->notThrows('Exception')
            ->testThatArgs(function($item, $arg){})->throws('Webbhuset\\Bifrost\\Core\\BifrostException')
            ->testThatArgs(function($item, $arg = 'def'){})->notThrows('Exception')
        ;
    }

    public static function processTest($test)
    {
        $test->newInstance(function($item) {
            return [
                'b'  => $item['a'],
                'bb' => $item['aa'],
            ];
        });

        $test->testThatArgs([
                [
                    'a' => 1,
                    'aa' => 2,
                ],
                [
                    'a' => 3,
                    'aa' => 4,
                ],
            ])
            ->returnsGenerator()
            ->returnsValue([
                [
                    'b' => 1,
                    'bb' => 2,
                ],
                [
                    'b' => 3,
                    'bb' => 4,
                ],
            ]);

        $test->newInstance(function($item) {
            yield ['asdf'];
        });

        $test->testThatArgs([['asdf']])
            ->returnsGenerator()
            ->throws('Webbhuset\\Bifrost\\Core\\BifrostException');
    }
}
