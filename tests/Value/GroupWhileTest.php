<?php

namespace Webbhuset\Pipeline\Test\Value;

use Eris\Generator;
use Webbhuset\Pipeline\Constructor as F;

final class GroupWhileTest extends \PHPUnit\Framework\TestCase
{
    use \Eris\TestTrait;


    public function testGroupWhile()
    {
        $this->forAll(
            Generator\Seq(Generator\nat())
        )
        ->then(function ($input) {
            $callback = function ($value, $batch) {
                return !$batch || reset($batch) == $value;
            };

            $fun = F::GroupWhile($callback);
            $result = iterator_to_array($fun($input));

            $resultValues = [];
            foreach ($result as $group) {
                $resultValues = array_merge($resultValues, $group);

                $this->assertNotEquals(
                    0,
                    count($group),
                    'The size of a result group should never be 0.'
                );

                $this->assertEquals(
                    1,
                    count(array_unique($group)),
                    'The values in a result group should all be the same.'
                );
            }

            $this->assertSame(
                $resultValues,
                $input,
                'The result should have the same values in the same order as the input, but grouped.'
            );
        });
    }
}
