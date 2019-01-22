<?php

namespace Webbhuset\Pipeline\Test\Value;

use Eris\Generator;
use Webbhuset\Pipeline\Constructor as F;

final class DeferTest extends \PHPUnit\Framework\TestCase
{
    use \Eris\TestTrait;


    public function testDefer()
    {
        $this->forAll(
            Generator\Seq(Generator\nat())
        )
        ->then(function ($input) {
            $map = function ($value) {
                return $value * 3;
            };

            $count = 0;
            $fun = F::Defer(function() use (&$count, $map)  {
                $count++;

                return F::Map($map);
            });
            $result = iterator_to_array($fun($input));

            $expected = array_map($map, $input);

            $this->assertSame(
                $result,
                $expected,
                'The result should be the input after applying the inner function.'
            );

            $this->assertEquals(
                $count,
                1,
                'The inner function should only be run once per input.'
            );
        });
    }
}
