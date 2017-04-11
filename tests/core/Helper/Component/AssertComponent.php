<?php

namespace Webbhuset\Bifrost\Test\Helper\Component;

use Webbhuset\Bifrost\Component;
use Exception;

class AssertComponent
{
    public static function makeAssert($items, $expected, $monad = null)
    {
        $testCallback   = [__CLASS__ , 'testPipeline'];

        return function ($pipeline, $instance, $e) use ($testCallback, $items, $expected, $monad) {
            if ($e) {
                return $e;
            }
            return $testCallback($pipeline, $items, $expected, $monad);
        };
    }

    public static function testPipeline($pipeline, $items, $expected, $monad = null)
    {
        $gen = $pipeline->process($items);

        if ($monad) {
            $monad  = new Component\Monad\Standard($monad, true);
            $gen    = $monad->process($gen);
        }
        $result = $e = null;

        try {
            $result = [];
            foreach ($gen as $key => $item) {
                if (is_string($key)) {
                    continue;
                }
                $result[] = $item;
            }
        } catch (Exception $e) {
            $result = $e;
        }

        if (is_object($expected) && $result instanceof $expected) {
            return;
        }

        if ($result instanceof Exception) {
            return $result;
        }

        return $result === $expected
                ? null
                : 'Items after pipeline does not match expected result.';
    }

}
