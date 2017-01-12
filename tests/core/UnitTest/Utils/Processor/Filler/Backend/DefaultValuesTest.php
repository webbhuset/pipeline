<?php
namespace Webbhuset\Bifrost\Core\Test\UnitTest\Utils\Processor\Filler\Backend;

class DefaultValuesTest
{
    public static function __constructTest()
    {
    }

    public static function getDataTest($test)
    {
        $defaults = [
                'price' => [
                    'test' => [
                        'EUR' => 132,
                        'SEK' => [],
                        'NOK' => 83,
                    ]
                ],
        ];
        $params = [
            'default_values' => $defaults,
        ];
        $test->newInstance($params)
            ->testThatArgs(
                [
                    ['price'=>[]]
                ]
            )->returnsInstanceOf('Webbhuset\Bifrost\Core\Utils\Processor\Filler\Backend\DefaultValues');
    }

    public static function offsetExistsTest()
    {
    }

    public static function offsetGetTest()
    {
    }

    public static function offsetSetTest()
    {
    }

    public static function offsetUnsetTest()
    {
    }

    public static function initTest()
    {
    }

    public static function processNextTest()
    {
    }

    public static function finalizeTest()
    {
    }

    public static function countTest()
    {
    }

    public static function getNextStepsTest()
    {
    }
}
