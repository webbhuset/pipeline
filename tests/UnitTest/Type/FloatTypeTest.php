<?php
namespace Webbhuset\Bifrost\Test\UnitTest\Type;

use Webbhuset\Bifrost\Type\TypeConstructor AS T;

class FloatTypeTest
{
    public static function __constructTest($test)
    {
    }

    public static function isEqualTest($test)
    {
        $test->newInstance()
            ->testThatArgs(514.0, 514.0)->returnsTrue()
            ->testThatArgs(-45514.0, -45514.0)->returnsTrue()
            ->testThatArgs(614.002, 614.002)->returnsTrue()
            ->testThatArgs(61.00000000002, 61.00000000001)->returnsTrue()
            ->testThatArgs(0.000, 0.000000)->returnsTrue();
    }

    public static function getErrorsTest($test)
    {
        $test->newInstance(T::NULLABLE)
            ->testThatArgs(231.123)->returnsFalse()
            ->testThatArgs(null)->returnsFalse()
            ->testThatArgs('124.123')->notReturnsFalse()
            ->testThatArgs(12)->notReturnsFalse()
            ->testThatArgs([12])->notReturnsFalse();

        $test->newInstance()
            ->testThatArgs(9867.00)->returnsFalse()
            ->testThatArgs(null)->notReturnsFalse()
            ->testThatArgs('9867.123')->notReturnsFalse()
            ->testThatArgs(123)->notReturnsFalse()
            ->testThatArgs([12])->notReturnsFalse();

        $test->newInstance(T::MIN(-4.5), T::NULLABLE)
            ->testThatArgs(-4.6)->notReturnsFalse()
            ->testThatArgs(5.5)->returnsFalse()
            ->testThatArgs(null)->returnsFalse();

        $test->newInstance(T::MAX(40.5), T::NULLABLE)
            ->testThatArgs(5.127)->returnsFalse()
            ->testThatArgs(40.556)->notReturnsFalse()
            ->testThatArgs(null)->returnsFalse();
    }

    public static function castTest($test)
    {
        $test->newInstance(T::NULLABLE)
            ->testThatArgs(12.36)->returnsValue(12.36)
            ->testThatArgs(null)->returnsNull()
            ->testThatArgs('123')->returnsValue(123.0)
            ->testThatArgs(12)->returnsValue(12.0);

        $test->newInstance()
            ->testThatArgs(12.36)->returnsValue(12.36)
            ->testThatArgs(null)->returnsNull()
            ->testThatArgs('123')->returnsValue(123.0)
            ->testThatArgs(12)->returnsValue(12.0);
    }

    public static function diffTest($test)
    {
        $test->newInstance()
            ->testThatArgs('fisk', null)->throws('Webbhuset\Bifrost\BifrostException');
    }
}
