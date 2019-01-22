<?php

namespace Webbhuset\Pipeline\Test\Value;

use Eris\Generator;
use Webbhuset\Pipeline\Constructor as F;

final class ForkTest extends \PHPUnit\Framework\TestCase
{
    use \Eris\TestTrait;


    public function testFork()
    {
        $this->forAll(
            Generator\Seq(Generator\nat())
        )
        ->then(function ($input) {
            $map = function ($value) {
                return $value * 3;
            };
            $filter = function ($value) {
                return $value >= 10;
            };

            $fun = F::Fork([
                F::Map($map),
                F::Filter($filter),
                F::Take(5),
            ]);

            $result = iterator_to_array($fun($input));

            $expected = [];
            $count = 0;
            foreach ($input as $value) {
                $expected[] = $map($value);

                if ($filter($value)) {
                    $expected[] = $value;
                }

                if ($count < 5) {
                    $count++;
                    $expected[] = $value;
                }
            }

            $this->assertSame(
                $result,
                $expected,
                'The result should be the input after applying all functions.'
            );
        });
    }
}
