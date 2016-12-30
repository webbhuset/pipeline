<?php
namespace Webbhuset\Bifrost\Test\Unit\Utils\Processor;
use Webbhuset\Bifrost\Core\Utils\Logger\NullLogger;
use Webbhuset\Bifrost\Core\Utils\Processor\Mock;
use Webbhuset\Bifrost\Core\Utils\Type as Type;

class Differ extends \Webbhuset\Bifrost\Test\TestAbstract
{

    public function initTest()
    {
    }

    public function countTest()
    {
    }

    public function processNextTest()
    {
        $structParams = [
            'fields' => [
                'sku'   =>  new Type\StringType(),
                'price' =>  new Type\IntType(),
            ]
        ];
        $type          = new Type\StructType($structParams);
        $nullLogger    = new NullLogger;
        $mockProcessor = new Mock;
        $indata = [
            [
                'old' => [
                        'sku'   => '123',
                        'price' => 53,
                ],
                'new' => [
                        'sku'   => '123',
                        'price' => 53,
                ],
            ],
            [
                'old' => [
                        'sku'   => '123',
                        'price' => 53,
                ],
                'new' => [
                        'sku'   => '123',
                        'price' => 567,
                ],
            ]
        ];

        $params = [
            'type' => $type
        ];
        $this->newInstance($nullLogger, $mockProcessor, $params)
            ->testThatArgs($indata)->returns(Null);
    }

    public function finalizeTest()
    {
    }
}
