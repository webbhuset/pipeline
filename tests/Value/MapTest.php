<?php

namespace Webbhuset\Pipeline\Test\Value;

use Eris\Generator;
use Webbhuset\Pipeline\Constructor as F;

final class MapTest extends \PHPUnit\Framework\TestCase
{
    use \Eris\TestTrait;


    public function testMap()
    {
        $this->forAll(
            Generator\Seq(Generator\nat())
        )
        ->then(function ($input) {
            $callback = function ($value) {
                return $value * 2;
            };
            $fun = F::Map($callback);

            $result = iterator_to_array($fun($input));

            $expected = array_map($callback, $input);

            $this->assertSame(
                $result,
                $expected,
                'The result should have the same values in the same order as the input, but each multiplied by 2.'
            );
        });
    }
}
