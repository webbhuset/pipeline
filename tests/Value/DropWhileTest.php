<?php

namespace Webbhuset\Pipeline\Test\Value;

use Eris\Generator;
use Webbhuset\Pipeline\Constructor as F;

final class DropWhileTest extends \PHPUnit\Framework\TestCase
{
    use \Eris\TestTrait;


    public function testDropWhile()
    {
        $this->forAll(
            Generator\nat(),
            Generator\Seq(Generator\nat())
        )
        ->then(function ($limit, $input) {
            $callback = function ($value) use ($limit) {
                return $value < $limit;
            };

            $fun = F::DropWhile($callback);
            $result = iterator_to_array($fun($input));

            $skip = 0;
            foreach ($input as $value) {
                if (!$callback($value)) {
                    break;
                }
                $skip++;
            }
            $expected = array_slice($input, $skip);

            $this->assertSame(
                $result,
                $expected,
                'The result should be the same as the input minus the dropped values.'
            );
        });
    }
}
