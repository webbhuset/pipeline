<?php

namespace Webbhuset\Pipeline\Test\Value;

use Eris\Generator;
use Webbhuset\Pipeline\Constructor as F;

final class ExpandTest extends \PHPUnit\Framework\TestCase
{
    use \Eris\TestTrait;


    public function testExpand()
    {
        $this->forAll(
            Generator\Seq(Generator\Seq(Generator\nat()))
        )
        ->then(function ($input) {
            $fun = F::Expand();
            $result = iterator_to_array($fun($input));

            $expected = [];
            foreach ($input as $array) {
                $expected = array_merge($expected, $array);
            }

            $this->assertSame(
                $result,
                $expected,
                'The result should have the same values in the same order as the input.'
            );
        });
    }
}
