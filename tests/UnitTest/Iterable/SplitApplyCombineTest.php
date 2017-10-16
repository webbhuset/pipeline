<?php

namespace Webbhuset\Whaskell\Test\UnitTest\Iterable;

use Webbhuset\Whaskell\Constructor as F;

class SplitApplyCombineTest
{
    public static function __constructTest($test)
    {

    }

    public static function __invokeTest($test)
    {
        $test->newInstance(
            function($item) {
                foreach ($item['numbers'] as $number) {
                    yield $number;
                }
            },
            F::Map(function($number) {
                return $number + 1;
            }),
            function($item, $numbers) {
                $item['numbers'] = $numbers;

                return $item;
            }
        );

        $test
            ->testThatArgs([[
                'numbers' => [
                    1,
                    2,
                    3,
                ],
            ]])
            ->returnsGenerator()
            ->returnsValue([[
                'numbers' => [
                    2,
                    3,
                    4,
                ]
            ]]);
    }

    public static function registerObserverTest($test)
    {

    }
}
