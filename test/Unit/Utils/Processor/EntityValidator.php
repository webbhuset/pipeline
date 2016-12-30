<?php
namespace Webbhuset\Bifrost\Test\Unit\Utils\Processor;
use Webbhuset\Bifrost\Core\Utils\Logger\NullLogger;
use Webbhuset\Bifrost\Core\Utils\Processor\Mock;
use Webbhuset\Bifrost\Core\Utils\Type as Type;

class EntityValidator extends \Webbhuset\Bifrost\Test\TestAbstract
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
                'sku'   => '123',
                'price' => 53,
            ],
            [
                'sku'   => 125,
                'price' => 53,
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
