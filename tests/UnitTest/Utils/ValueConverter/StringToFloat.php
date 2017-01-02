<?php
namespace Webbhuset\Bifrost\Test\Unit\Utils\ValueConverter;
use Webbhuset\Bifrost\Core\Utils\Type as Core;

class StringToFloat extends \Webbhuset\Bifrost\Test\TestAbstract
{
    public function convertTest()
    {
        $this->newInstance()
            ->testThatArgs('123')->returns(123.0)
            ->testThatArgs('6.787')->returns(6.787);
    }
}
