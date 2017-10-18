<?php

namespace Webbhuset\Whaskell\Test\UnitTest\Iterable;

use Webbhuset\Whaskell\Constructor as F;

class ApplyCombineTest
{
    public static function __constructTest($test)
    {

    }

    public static function __invokeTest($test)
    {
        /**
         * @testCase Basic functionality test.
         */
        $test->newInstance(
            [
                F::Expand(function($item) {
                    foreach ($item['numbers'] as $number) {
                        yield $number;
                    }
                }),
                F::Map(function($number) {
                    return $number + 1;
                }),
            ],
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

        /**
         * @testCase Test that function returns properly with a group inside.
         */
        $test->newInstance(
            [
                F::Group(3),
                F::Expand(),
            ],
            function($item, $result) {
                return reset($result);
            }
        );

        $test->testThatArgs([1, 2, 3, 4, 5])
            ->returnsGenerator()
            ->returnsValue([1, 2, 3, 4, 5]);
    }

    public static function registerObserverTest($test)
    {

    }
}
