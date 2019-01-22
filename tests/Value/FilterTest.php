<?php

namespace Webbhuset\Pipeline\Test\Value;

use Eris\Generator;
use Webbhuset\Pipeline\Constructor as F;

final class FilterTest extends \PHPUnit\Framework\TestCase
{
    use \Eris\TestTrait;


    public function testFilter()
    {
        $this->forAll(
            Generator\nat(),
            Generator\Seq(Generator\nat())
        )
        ->then(function ($limit, $input) {
            $callback = function ($value) use ($limit) {
                return $value < $limit;
            };

            $fun = F::Filter($callback);
            $result = iterator_to_array($fun($input));

            $expected = array_values(array_filter($input, $callback));

            $this->assertSame(
                $result,
                $expected,
                'The result should be the same as the input minus the filtered values.'
            );
        });
    }
}
