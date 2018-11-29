<?php

namespace Webbhuset\Whaskell\Test\Iterable;

use Webbhuset\Whaskell\Constructor as F;
use Eris\Generator;

final class TakeTest extends \PHPUnit\Framework\TestCase
{
    use \Eris\TestTrait;

    public function testTake5()
    {
        $take5 = F::Take(5);

        $this
            ->forAll(
                Generator\seq(Generator\nat())
            )
            ->then(function($array) use ($take5) {
                $result = iterator_to_array($take5($array));

                $this->assertLessThanOrEqual(5, count($result));
            });
    }
}
