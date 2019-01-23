<?php

namespace Webbhuset\Pipeline\Test\Value;

use Eris\Generator;
use Webbhuset\Pipeline\Constructor as F;

final class DropTest extends \PHPUnit\Framework\TestCase
{
    use \Eris\TestTrait;


    public function testDrop()
    {
        $this->forAll(
            Generator\choose(2, 100),
            Generator\Seq(Generator\nat())
        )
        ->then(function ($amount, $input) {
            $fun = F::Drop($amount);
            $result = iterator_to_array($fun($input));

            $this->assertEquals(
                count($result),
                max(0, count($input) - $amount),
                'The size of the result should be equal to count($input) - $amount.'
            );

            $this->assertSame(
                $result,
                array_slice($input, $amount),
                'The result should be the same as the input minus the dropped values.'
            );
        });
    }
}
