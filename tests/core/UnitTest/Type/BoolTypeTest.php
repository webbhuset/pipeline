<?php
namespace Webbhuset\Bifrost\Core\Test\UnitTest\Type;

class BoolTypeTest
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
            ->testThatArgs(true, true)->returnsValue(true)
            ->testThatArgs(false, false)->returnsValue(true)
            ->testThatArgs(true, false)->returnsValue(false);
    }

    public static function getErrorsTest($test)
    {
        $test->newInstance()
            ->testThatArgs(false)->returnsValue(false)
            ->testThatArgs(true)->returnsValue(false)
            ->testThatArgs(null)->returnsValue(false)
            ->testThatArgs('false')->notReturnsValue(false)
            ->testThatArgs('1')->notReturnsValue(false)
            ->testThatArgs(1)->notReturnsValue(false)
            ->testThatArgs([true])->notReturnsValue(false);

        $test->newInstance(['required' => true])
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

        $test->newInstance(['required' => true])
            ->testThatArgs(1)->returnsValue(true)
            ->testThatArgs(0)->returnsValue(false)
            ->testThatArgs(null)->returnsValue(null)
            ->testThatArgs('false')->returnsValue(true)
            ->testThatArgs('')->returnsValue(false);
    }

    public static function diffTest($test)
    {
        $test->newInstance()
            ->testThatArgs(null, null)->throws('Webbhuset\Bifrost\Core\BifrostException');
    }
}
