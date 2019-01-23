<?php

namespace Webbhuset\Pipeline\Test\Value;

use Eris\Generator;
use Webbhuset\Pipeline\Constructor as F;

final class TakeWhileTest extends \PHPUnit\Framework\TestCase
{
    use \Eris\TestTrait;


    public function testTakeWhile()
    {
        $this->forAll(
            Generator\nat(),
            Generator\Seq(Generator\nat())
        )
        ->then(function ($limit, $input) {
            $callback = function ($value) use ($limit) {
                return $value < $limit;
            };

            $fun = F::TakeWhile($callback);
            $result = iterator_to_array($fun($input));

            $expected = [];
            foreach ($input as $value) {
                if (!$callback($value)) {
                    break;
                }
                $expected[] = $value;
            }

            $this->assertSame(
                $result,
                $expected,
                'The result should be the same as the input minus the dropped values.'
            );
        });
    }
}
