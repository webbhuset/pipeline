<?php

namespace Webbhuset\Pipeline\Test\Value;

use Eris\Generator;
use Webbhuset\Pipeline\Constructor as F;

final class ReduceTest extends \PHPUnit\Framework\TestCase
{
    use \Eris\TestTrait;


    public function testReduce()
    {
        $this->forAll(
            Generator\Seq(Generator\nat())
        )
        ->then(function ($input) {
            $fun = F::Reduce(function ($value, $sum) {
                return $sum + $value;
            }, 0);
            $result = iterator_to_array($fun($input));

            $expected = [array_sum($input)];

            $this->assertSame(
                $result,
                $expected,
                'The result should be an array with the sum of every input value.'
            );
        });
    }
}
