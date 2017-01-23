<?php
namespace Webbhuset\Bifrost\Core\Test\UnitTest\Type;

class IntTypeTest
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
            ->throws('Webbhuset\Bifrost\Core\BifrostException');
    }

    public static function isEqualTest($test)
    {
        $test->newInstance()
            ->testThatArgs(5123, 5123)->returnsValue(true)
            ->testThatArgs(-45123, -45123)->returnsValue(true)
            ->testThatArgs(0, 0)->returnsValue(true);
            /*->testThatArgs('0', 0)->returnsValue(false)
            ->testThatArgs(null, 0)->returnsValue(false)
            ->testThatArgs(0, [])->returnsValue(false)
            ->testThatArgs(5123, -5123)->returnsValue(false);*/
    }

    public static function getErrorsTest($test)
    {
        $test->newInstance()
            ->testThatArgs(231)->returnsValue(false)
            ->testThatArgs(null)->returnsValue(false)
            ->testThatArgs('12')->notReturnsValue(false)
            ->testThatArgs(12.123)->notReturnsValue(false)
            ->testThatArgs([12])->notReturnsValue(false);

        $test->newInstance(['required' => true])
            ->testThatArgs(9867)->returnsValue(false)
            ->testThatArgs(null)->notReturnsValue(false)
            ->testThatArgs('9867')->notReturnsValue(false)
            ->testThatArgs(12.123)->notReturnsValue(false)
            ->testThatArgs([12])->notReturnsValue(false);

        $test->newInstance(['min_value' => 4])
            ->testThatArgs(-5)->notReturnsValue(false)
            ->testThatArgs(5)->returnsValue(false)
            ->testThatArgs(null)->returnsValue(false);

        $test->newInstance(['max_value' => 40])
            ->testThatArgs(5)->returnsValue(false)
            ->testThatArgs(55)->notReturnsValue(false)
            ->testThatArgs(null)->returnsValue(false);
    }

    public static function castTest($test)
    {
        $test->newInstance()
            ->testThatArgs(12)->returnsValue(12)
            ->testThatArgs(null)->returnsValue(null)
            ->testThatArgs('123')->returnsValue(123)
            ->testThatArgs(12.0)->returnsValue(12);


        $test->newInstance(['required' => true])
            ->testThatArgs(12)->returnsValue(12)
            ->testThatArgs(null)->returnsValue(null)
            ->testThatArgs('123')->returnsValue(123)
            ->testThatArgs(12.0)->returnsValue(12);
    }

    public static function diffTest($test)
    {
        $test->newInstance()
            ->testThatArgs(null, null)->throws('Webbhuset\Bifrost\Core\BifrostException');
    }
}
