<?php

namespace Webbhuset\Pipeline\Test\Value;

use Eris\Generator;
use Webbhuset\Pipeline\Constructor as F;

final class MultiplexTest extends \PHPUnit\Framework\TestCase
{
    use \Eris\TestTrait;


    public function testMultiplex()
    {
        $this->forAll(
            Generator\Seq(Generator\nat())
        )
        ->then(function ($input) {
            $isEven = function ($value) {
                return $value % 2 == 0;
            };
            $double = function ($value) {
                return $value * 2;
            };
            $half = function ($value) {
                return $value / 2;
            };

            $fun = F::Multiplex(
                $isEven,
                [
                    true => F::Map($half),
                    false => F::Map($double),
                ]
            );
            $result = iterator_to_array($fun($input));

            $expected = [];
            foreach ($input as $value) {
                if ($isEven($value)) {
                    $expected[] = $half($value);
                } else {
                    $expected[] = $double($value);
                }
            }

            $this->assertSame(
                $result,
                $expected,
                'The result should be the input after applying the correct function.'
            );
        });
    }
}
