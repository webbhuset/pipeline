<?php

namespace Webbhuset\Whaskell\Test\Iterable;

use Eris\Generator;
use Webbhuset\Whaskell\Constructor as F;

final class GroupCountTest extends \PHPUnit\Framework\TestCase
{
    use \Eris\TestTrait;


    public function testGroupCount()
    {
        $this
            ->forAll(
                Generator\choose(2, 100),
                Generator\seq(Generator\nat())
            )
            ->then(function($size, $input) {
                $fun    = F::GroupCount($size);
                $result = iterator_to_array($fun($input));

                $this->assertEquals(
                    ceil(count($input) / $size),
                    count($result),
                    'The size of the result should be equal to ceil(count($input) / $size).'
                );

                foreach ($result as $group) {
                    $this->assertLessThanOrEqual(
                        $size,
                        count($group),
                        'The size of a result group should never be larger than $size.'
                    );
                    $this->assertNotEquals(
                        0,
                        count($group),
                        'The size of a result group should never be 0.'
                    );
                }
            });
    }
}
