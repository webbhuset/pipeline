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
            ->testThatArgs([])->returnsValue($defaults)
            ->testThatArgs(['price'=>[]])->returnsValue($defaults);
    }
}
