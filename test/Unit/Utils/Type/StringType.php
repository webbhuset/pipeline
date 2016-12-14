<?php
namespace Webbhuset\Bifrost\Test\Unit\Utils\Type;
use Webbhuset\Bifrost\Core\Utils\Type as Core;

class StringType extends \Webbhuset\Bifrost\Test\TestAbstract
    implements \Webbhuset\Bifrost\Test\TestInterface
{
    protected function isEqualTest()
    {
        $this
            ->newInstance()
            ->testThatArgs('12345', '12345')
            ->returns(true);

        $this
            ->newInstance()
            ->testThatArgs('12345', '1345')
            ->notReturns(false);

            /*
            ->returnsOneOf([true, 'hej', 1])
            ->returnsType('string')
            ->returnsIntGt(4)
            ->returnsFloatGt(4.2)
            ->throws('Webbhuset\\Bifrost\\Exception')
            ->notThrows('Exception')
            ->notReturns('result');
            */

        return;
        $stringType = new Core\StringType();
        $method     = [$stringType, "isEqual"];
        $this->testMethod($method, ['apa123', 'apa123'], ['equal' => true]);
        $this->testMethod($method, ['', ''], ['equal' => true]);
        $this->testMethod($method, ['apa123', 'apa'], ['equal' => false]);
        $this->testMethod($method, ['apa', 'apa123'], ['equal' => false]);
        $this->testMethod($method, ['', null], ['equal' => false]);
        $this->testMethod($method, [null, ''], ['equal' => false]);
        $this->testMethod($method, [0, ''], ['equal' => false]);
        $this->testMethod($method, [0, []], ['equal' => false]);
    }

    protected function getErrorsTest()
    {
        $this
            ->newInstance()
            ->testThatArgs('12345')
            ->returns(false)

            ->testThatArgs(null)
            ->returns(false)

            ->testThatArgs(12)
            ->returnsType('string')

            ->testThatArgs([123])
            ->returnsType('string');


        $stringType = new Core\StringType();
        $method     = [$stringType, "getErrors"];
        $this->testMethod($method, ['apa123'], ['equal' => false]);
        $this->testMethod($method, [null], ['equal' => false]);
        $this->testMethod($method, [12], ['not_equal' => false]);
        $this->testMethod($method, [12.123], ['not_equal' => false]);
        $this->testMethod($method, [['12']], ['not_equal' => false]);

        $stringType = new Core\StringType(['required' => true]);
        $method     = [$stringType, "getErrors"];
        $this->testMethod($method, ['apa123'], ['equal' => false]);
        $this->testMethod($method, [null], ['not_equal' => false]);
        $this->testMethod($method, [12], ['not_equal' => false]);
        $this->testMethod($method, [12.123], ['not_equal' => false]);
        $this->testMethod($method, [['12']], ['not_equal' => false]);

        $stringType = new Core\StringType(['min_length' => 4]);
        $method     = [$stringType, "getErrors"];
        $this->testMethod($method, ['apa'], ['not_equal' => false]);
        $this->testMethod($method, ['apa123'], ['equal' => false]);
        $this->testMethod($method, [null], ['equal' => false]);

        $stringType = new Core\StringType(['max_length' => 4]);
        $method     = [$stringType, "getErrors"];
        $this->testMethod($method, ['apa'], ['equal' => false]);
        $this->testMethod($method, ['apa123'], ['not_equal' => false]);
        $this->testMethod($method, [null], ['equal' => false]);
    }

    protected function sanitizeTest()
    {
        $stringType = new Core\StringType();
        $method     = [$stringType, "sanitize"];
        $this->testMethod($method, ['apa123'], ['equal' => 'apa123']);
        $this->testMethod($method, [null], ['equal' => null]);
        $this->testMethod($method, [123], ['equal' => '123']);
        $this->testMethod($method, [12.335], ['equal' => '12.335']);

        $stringType = new Core\StringType(['required' => true]);
        $method     = [$stringType, "sanitize"];
        $this->testMethod($method, ['apa123'], ['equal' => 'apa123']);
        $this->testMethod($method, [null], ['equal' => null]);
        $this->testMethod($method, [123], ['equal' => '123']);
        $this->testMethod($method, [12.335], ['equal' => '12.335']);
    }
}
