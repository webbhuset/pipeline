<?php
namespace Webbhuset\Bifrost\Test\UnitTest\Type;
use Webbhuset\Bifrost\Type\AbstractType;

class AbstractTypeTest extends AbstractType
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
    }

    public static function getErrorsTest($test)
    {
        $test->newInstance()
            ->testThatArgs(null)->returnsValue(false);

        $test->newInstance(['required' => true])
            ->testThatArgs(null)->notReturnsValue(false);
    }

    public static function castTest($test)
    {
    }

    public static function diffTest($test)
    {
        $test->newInstance()
            ->testThatArgs(null, null)->throws('Webbhuset\Bifrost\BifrostException');
    }

    public function isEqual($a, $b)
    {
    }
    public function cast($a)
    {
    }
}
