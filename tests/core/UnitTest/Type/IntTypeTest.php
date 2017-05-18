<?php
namespace Webbhuset\Bifrost\Test\UnitTest\Type;

use Webbhuset\Bifrost\Type\TypeConstructor AS T;

class IntTypeTest
{
    public static function __constructTest($test)
    {
    }

    public static function isEqualTest($test)
    {
        $test->newInstance()
            ->testThatArgs(5123, 5123)->returnsTrue()
            ->testThatArgs(-45123, -45123)->returnsTrue()
            ->testThatArgs(0, 0)->returnsTrue();
    }

    public static function getErrorsTest($test)
    {
        $test->newInstance(T::NULLABLE)
            ->testThatArgs(231)->returnsFalse()
            ->testThatArgs(null)->returnsFalse()
            ->testThatArgs('12')->notReturnsFalse()
            ->testThatArgs(12.123)->notReturnsFalse()
            ->testThatArgs([12])->notReturnsFalse();

        $test->newInstance()
            ->testThatArgs(9867)->returnsFalse()
            ->testThatArgs(null)->notReturnsFalse()
            ->testThatArgs('9867')->notReturnsFalse()
            ->testThatArgs(12.123)->notReturnsFalse()
            ->testThatArgs([12])->notReturnsFalse();

        $test->newInstance(T::MIN(4), T::NULLABLE)
            ->testThatArgs(-5)->notReturnsValue(false)
            ->testThatArgs(5)->returnsValue(false)
            ->testThatArgs(null)->returnsValue(false);

        $test->newInstance(T::MAX(40), T::NULLABLE)
            ->testThatArgs(5)->returnsValue(false)
            ->testThatArgs(55)->notReturnsValue(false)
            ->testThatArgs(null)->returnsValue(false);
    }

    public static function castTest($test)
    {
        $test->newInstance(T::NULLABLE)
            ->testThatArgs(12)->returnsValue(12)
            ->testThatArgs(null)->returnsValue(null)
            ->testThatArgs('123')->returnsValue(123)
            ->testThatArgs(12.0)->returnsValue(12);


        $test->newInstance()
            ->testThatArgs(12)->returnsValue(12)
            ->testThatArgs(null)->returnsValue(null)
            ->testThatArgs('123')->returnsValue(123)
            ->testThatArgs(12.0)->returnsValue(12);
    }

    public static function diffTest($test)
    {
        $test->newInstance()
            ->testThatArgs('fisk', null)->throws('Webbhuset\Bifrost\BifrostException');
    }
}
