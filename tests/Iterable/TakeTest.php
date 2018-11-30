<?php

namespace Webbhuset\Whaskell\Test\Iterable;

use Eris\Generator;
use Webbhuset\Whaskell\Constructor as F;

final class TakeTest extends \PHPUnit\Framework\TestCase
{
    use \Eris\TestTrait;


    public function testTake()
    {
        $this
            ->forAll(
                Generator\nat(),
                Generator\seq(Generator\nat())
            )
            ->then(function($amount, $input) {
                $fun    = F::Take($amount);
                $result = iterator_to_array($fun($input));

                $this->assertEquals(
                    min(count($input), $amount),
                    count($result),
                    'The size of the result should be equal to min(count($input), $amount).'
                );
            });
    }

    public function testNegativeAmount()
    {
        $this->expectNotToPerformAssertions();

        $this
            ->forAll(
                Generator\neg()
            )
            ->then(function($amount) {
                try {
                    F::Take($amount);
                } catch (\InvalidArgumentException $e) {
                    return;
                }

                $this->fail('A negative $amount should throw an exception.');
            });
    }

    public function testNonIntAmount()
    {
        $this->expectNotToPerformAssertions();

        $this
            ->forAll(
                Generator\string()
            )
            ->then(function($amount) {
                try {
                    F::Take($amount);
                } catch (\InvalidArgumentException $e) {
                    return;
                }

                $this->fail('A string $amount should throw an exception.');
            });
    }
}
