<?php

namespace Webbhuset\Pipeline\Test\Value;

use Eris\Generator;
use Webbhuset\Pipeline\Constructor as F;

final class ObserveTest extends \PHPUnit\Framework\TestCase
{
    use \Eris\TestTrait;


    public function testObserve()
    {
        $this->forAll(
            Generator\seq(Generator\nat())
        )
        ->then(function ($input) {
            $count = 0;
            $fun = F::Observe(function ($value) use (&$count) {
                $count++;
            });
            $result = iterator_to_array($fun($input));

            $this->assertSame(
                $input,
                $result,
                'The result should be exactly the same as the input.'
            );

            $this->assertEquals(
                $count,
                count($input),
                'The callback function should be run once per input value.'
            );
        });
    }
}
