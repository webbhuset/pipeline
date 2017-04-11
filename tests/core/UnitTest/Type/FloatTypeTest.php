<?php
namespace Webbhuset\Bifrost\Test\UnitTest\Type;

class FloatTypeTest
{
    public static function __constructTest($test)
    {
        $params = ['required' => false];
        $test->testThatArgs($params)
            ->notThrows('Exception');

        $params = ['required' => true];
        $test->testThatArgs($params)
            ->notThrows('Exception');

        $params = ['required' => 'apa'];
        $test->testThatArgs($params)
            ->throws('Webbhuset\Bifrost\BifrostException');
    }

    public static function isEqualTest($test)
    {
        $test->newInstance()
            ->testThatArgs(514.0, 514.0)->returnsValue(true)
            ->testThatArgs(-45514.0, -45514.0)->returnsValue(true)
            ->testThatArgs(614.002, 614.002)->returnsValue(true)
            ->testThatArgs(61.00000000002, 61.00000000001)->returnsValue(true)
            ->testThatArgs(0.000, 0.000000)->returnsValue(true);
            /*->testThatArgs('0', 0.0)->returnsValue(false)
            ->testThatArgs(null, 0.0)->returnsValue(false)
            ->testThatArgs([], 0.0)->throws();*/
    }

    public static function getErrorsTest($test)
    {
        $test->newInstance()
            ->testThatArgs(231.123)->returnsValue(false)
            ->testThatArgs(null)->returnsValue(false)
            ->testThatArgs('124.123')->notReturnsValue(false)
            ->testThatArgs(12)->notReturnsValue(false)
            ->testThatArgs([12])->notReturnsValue(false);

        $test->newInstance(['required' => true])
            ->testThatArgs(9867.00)->returnsValue(false)
            ->testThatArgs(null)->notReturnsValue(false)
            ->testThatArgs('9867.123')->notReturnsValue(false)
            ->testThatArgs(123)->notReturnsValue(false)
            ->testThatArgs([12])->notReturnsValue(false);

        $test->newInstance(['min_value' => -4.5])
            ->testThatArgs(-4.6)->notReturnsValue(false)
            ->testThatArgs(5.5)->returnsValue(false)
            ->testThatArgs(null)->returnsValue(false);

        $test->newInstance(['max_value' => 40.5])
            ->testThatArgs(5.127)->returnsValue(false)
            ->testThatArgs(40.556)->notReturnsValue(false)
            ->testThatArgs(null)->returnsValue(false);
    }

    public static function castTest($test)
    {
        $test->newInstance()
            ->testThatArgs(12.36)->returnsValue(12.36)
            ->testThatArgs(null)->returnsValue(null)
            ->testThatArgs('123')->returnsValue(123.0)
            ->testThatArgs(12)->returnsValue(12.0);

        $test->newInstance(['required' => true])
            ->testThatArgs(12.36)->returnsValue(12.36)
            ->testThatArgs(null)->returnsValue(null)
            ->testThatArgs('123')->returnsValue(123.0)
            ->testThatArgs(12)->returnsValue(12.0);
    }

    public static function diffTest($test)
    {
        $test->newInstance()
            ->testThatArgs(null, null)->throws('Webbhuset\Bifrost\BifrostException');
    }
}
