<?php

namespace Webbhuset\Pipeline\Test\Value;

use Eris\Generator;
use Webbhuset\Pipeline\Constructor as F;

final class ObserveTest extends \PHPUnit\Framework\TestCase
{
    use \Eris\TestTrait;


    public function testObserve()
    {
        $observe = F::Observe(function ($value) {
            return null;
        });

        $this
            ->forAll(
                Generator\seq(Generator\nat())
            )
            ->then(function ($input) use ($observe) {
                $result = iterator_to_array($observe($input));

                $this->assertSame(
                    $input,
                    $result,
                    'The result should be exactly the same as the input.'
                );
            });
    }
}
