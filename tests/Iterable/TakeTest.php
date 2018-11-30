<?php

namespace Webbhuset\Whaskell\Test\Iterable;

use Webbhuset\Whaskell\Constructor as F;
use Eris\Generator;

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
            ->then(function($amount, $array) {
                $fun    = F::Take($amount);
                $result = iterator_to_array($fun($array));

                $this->assertLessThanOrEqual($amount, count($result));
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

                $this->fail('A negative amount should throw an exception.');
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

                $this->fail('A string amount should throw an exception.');
            });
    }
}
