<?php
namespace Webbhuset\Bifrost\Test\Unit\Utils\Type;
use Webbhuset\Bifrost\Core\Utils\Type as Core;

class FloatType extends \Webbhuset\Bifrost\Test\TestAbstract
    implements \Webbhuset\Bifrost\Test\TestInterface
{

    public function run() {
        $this->isEqualTest();
        $this->getErrorsTest();
        $this->sanitizeTest();

        parent::run();
    }

    protected function isEqualTest()
    {
        $floatType = new Core\FloatType();
        $method    = [$floatType, "isEqual"];
        $this->testMethod($method, [514.0, 514.0], ['equal' => true]);
        $this->testMethod($method, [-45514.0, -45514.0], ['equal' => true]);
        $this->testMethod($method, [614.002, 614.002], ['equal' => true]);
        $this->testMethod($method, [61.00000000002, 61.00000000001], ['equal' => true]);
        $this->testMethod($method, [0.000, 0.000000], ['equal' => true]);
        $this->testMethod($method, ['0', 0.0], ['equal' => false]);
        $this->testMethod($method, [null, 0.0], ['equal' => false]);
        $this->testMethod($method, [[], 0.0], ['equal' => false]);

    }

    protected function getErrorsTest()
    {
        $floatType = new Core\FloatType();
        $method    = [$floatType, "getErrors"];
        $this->testMethod($method, [231.123], ['equal' => false]);
        $this->testMethod($method, [null], ['equal' => false]);
        $this->testMethod($method, ['124.123'], ['not_equal' => false]);
        $this->testMethod($method, [12], ['not_equal' => false]);
        $this->testMethod($method, [[12]], ['not_equal' => false]);

        $floatType = new Core\FloatType(['required' => true]);
        $method    = [$floatType, "getErrors"];
        $this->testMethod($method, [9867.00], ['equal' => false]);
        $this->testMethod($method, [null], ['not_equal' => false]);
        $this->testMethod($method, ['9867.123'], ['not_equal' => false]);
        $this->testMethod($method, [123], ['not_equal' => false]);
        $this->testMethod($method, [[12]], ['not_equal' => false]);

        $floatType = new Core\FloatType(['min_value' => -4.5]);
        $method    = [$floatType, "getErrors"];
        $this->testMethod($method, [-4.6], ['not_equal' => false]);
        $this->testMethod($method, [5.5], ['equal' => false]);
        $this->testMethod($method, [null], ['equal' => false]);

        $floatType = new Core\FloatType(['max_value' => 40.5]);
        $method    = [$floatType, "getErrors"];
        $this->testMethod($method, [5.127], ['equal' => false]);
        $this->testMethod($method, [40.556], ['not_equal' => false]);
        $this->testMethod($method, [null], ['equal' => false]);
    }

    protected function sanitizeTest()
    {
        $floatType = new Core\FloatType();
        $method    = [$floatType, "sanitize"];
        $this->testMethod($method, [12.36], ['equal' => 12.36]);
        $this->testMethod($method, [null], ['equal' => null]);
        $this->testMethod($method, ['123'], ['equal' => 123.0]);
        $this->testMethod($method, [12], ['equal' => 12.0]);


        $floatType = new Core\FloatType(['required' => true]);
        $method    = [$floatType, "sanitize"];
        $this->testMethod($method, [12.36], ['equal' => 12.36]);
        $this->testMethod($method, [null], ['equal' => null]);
        $this->testMethod($method, ['123'], ['equal' => 123.0]);
        $this->testMethod($method, [12], ['equal' => 12.0]);
    }
}
