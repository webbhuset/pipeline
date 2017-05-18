<?php
namespace Webbhuset\Bifrost\Test\UnitTest\Type;

use Webbhuset\Bifrost\Type\TypeConstructor AS T;

class BoolTypeTest
{
    public static function __constructTest($test)
    {
        $test->testThatArgs(T::NULLABLE)
            ->notThrows('Exception');

        $test->testThatArgs()
            ->notThrows('Exception');
    }

    public static function isEqualTest($test)
    {
        $test->newInstance()
            ->testThatArgs(true, true)->returnsTrue()
            ->testThatArgs(false, false)->returnsTrue()
            ->testThatArgs(true, false)->returnsFalse();
    }

    public static function getErrorsTest($test)
    {
        $test->newInstance(T::NULLABLE)
            ->testThatArgs(null)->returnsFalse();

        $test->newInstance()
            ->testThatArgs(null)->notReturnsFalse();

        $test->newInstance(T::NULLABLE(true))
            ->testThatArgs(null)->returnsFalse();

        $test->newInstance(T::NULLABLE(false))
            ->testThatArgs(null)->notReturnsFalse();

        $test->newInstance(T::NULLABLE)
            ->testThatArgs(false)->returnsFalse()
            ->testThatArgs(true)->returnsFalse()
            ->testThatArgs(null)->returnsFalse();

        $test->newInstance()
            ->testThatArgs(null)->notReturnsFalse()
            ->testThatArgs('false')->notReturnsValue(false)
            ->testThatArgs('1')->notReturnsValue(false)
            ->testThatArgs(1)->notReturnsValue(false)
            ->testThatArgs([true])->notReturnsValue(false);

        $test->newInstance()
            ->testThatArgs(false)->returnsValue(false)
            ->testThatArgs(true)->returnsValue(false)
            ->testThatArgs(null)->notReturnsValue(false);

    }

    public static function castTest($test)
    {
        $test->newInstance()
            ->testThatArgs(1)->returnsValue(true)
            ->testThatArgs(0)->returnsValue(false)
            ->testThatArgs(null)->returnsValue(null)
            ->testThatArgs('false')->returnsValue(true)
            ->testThatArgs('')->returnsValue(false);

        $test->newInstance(T::NULLABLE)
            ->testThatArgs(1)->returnsValue(true)
            ->testThatArgs(0)->returnsValue(false)
            ->testThatArgs(null)->returnsValue(null)
            ->testThatArgs('false')->returnsValue(true)
            ->testThatArgs('')->returnsValue(false);
    }

    public static function diffTest($test)
    {
        $test->newInstance()
            ->testThatArgs(null, null)->throws('Webbhuset\Bifrost\BifrostException');
    }
}
