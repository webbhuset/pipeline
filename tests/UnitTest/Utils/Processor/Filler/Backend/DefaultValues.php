<?php
namespace Webbhuset\Bifrost\Test\Unit\Utils\Processor\Filler\Backend;
class defaultValues extends \Webbhuset\Bifrost\Test\TestAbstract
{
    public function __constructTest()
    {
    }

    public function getDataTest()
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
        $this->newInstance($params)
            ->testThatArgs([])->returns($defaults)
            ->testThatArgs(['price'=>[]])->returns($defaults);
    }
}
