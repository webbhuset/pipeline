<?php

namespace Webbhuset\Pipeline\Test\Value;

use Eris\Generator;
use Webbhuset\Pipeline\Constructor as F;

final class ComposeTest extends \PHPUnit\Framework\TestCase
{
    use \Eris\TestTrait;


    public function testCompose()
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

            $fun = F::Compose([
                F::Map($map),
                F::Filter($filter),
                F::Take(5),
            ]);
            $result = iterator_to_array($fun($input));

            $expected = array_map($map, $input);
            $expected = array_filter($expected, $filter);
            $expected = array_slice($expected, 0, 5);

            $this->assertSame(
                $result,
                $expected,
                'The result should be the input after applying all functions.'
            );
        });
    }

    public function testEmptyCompose()
    {
        $this->forAll(
            Generator\Seq(Generator\nat())
        )
        ->then(function ($input) {
            $fun = F::Compose([]);

            $result = $fun($input);

            $this->assertSame(
                $result,
                $input,
                'The result and input should be the same with an empty Compose.'
            );
        });
    }
}
