<?php

namespace Webbhuset\Pipeline\Test\Value;

use Eris\Generator;
use Webbhuset\Pipeline\Constructor as F;

final class ScanTest extends \PHPUnit\Framework\TestCase
{
    use \Eris\TestTrait;


    public function testScan()
    {
        $this->forAll(
            Generator\seq(Generator\nat())
        )
        ->then(function ($input) {
            $fun = F::Scan(function ($value, $sum) {
                return $sum + $value;
            }, 0);
            $result = iterator_to_array($fun($input));

            $expected = [0];
            $sum = 0;
            foreach ($input as $value) {
                $sum += $value;
                $expected[] = $sum;
            }

            $this->assertSame(
                $result,
                $expected,
                'The result should be the sum of every input value and every previous values.'
            );
        });
    }
}
