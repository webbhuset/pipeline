<?php
namespace Webbhuset\Bifrost\Test\Unit\Utils\Type;
use Webbhuset\Bifrost\Core\Utils\Type as Core;

class IntType extends \Webbhuset\Bifrost\Test\TestAbstract
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
        $intType = new Core\IntType();
        $method     = [$intType, "isEqual"];
        $this->testMethod($method, [514, 514], ['equal' => true]);
        $this->testMethod($method, [-45514, -45514], ['equal' => true]);
        $this->testMethod($method, [0, 0], ['equal' => true]);
        $this->testMethod($method, ['0', 0], ['equal' => false]);
        $this->testMethod($method, [null, 0], ['equal' => false]);
        $this->testMethod($method, [[], 0], ['equal' => false]);
        $this->testMethod($method, [514, -514], ['equal' => false]);

    }

    protected function getErrorsTest()
    {
        $intType = new Core\IntType();
        $method  = [$intType, "getErrors"];
        $this->testMethod($method, [231], ['equal' => false]);
        $this->testMethod($method, [null], ['equal' => false]);
        $this->testMethod($method, ['12'], ['not_equal' => false]);
        $this->testMethod($method, [12.123], ['not_equal' => false]);
        $this->testMethod($method, [[12]], ['not_equal' => false]);

        $intType = new Core\IntType(['required' => true]);
        $method  = [$intType, "getErrors"];
        $this->testMethod($method, [9867], ['equal' => false]);
        $this->testMethod($method, [null], ['not_equal' => false]);
        $this->testMethod($method, ['9867'], ['not_equal' => false]);
        $this->testMethod($method, [12.123], ['not_equal' => false]);
        $this->testMethod($method, [[12]], ['not_equal' => false]);

        $intType = new Core\IntType(['min_value' => -4]);
        $method  = [$intType, "getErrors"];
        $this->testMethod($method, [-5], ['not_equal' => false]);
        $this->testMethod($method, [5], ['equal' => false]);
        $this->testMethod($method, [null], ['equal' => false]);

        $intType = new Core\IntType(['max_value' => 40]);
        $method  = [$intType, "getErrors"];
        $this->testMethod($method, [5], ['equal' => false]);
        $this->testMethod($method, [55], ['not_equal' => false]);
        $this->testMethod($method, [null], ['equal' => false]);
    }

    protected function sanitizeTest()
    {
        $intType = new Core\IntType();
        $method  = [$intType, "sanitize"];
        $this->testMethod($method, [12], ['equal' => 12]);
        $this->testMethod($method, [null], ['equal' => null]);
        $this->testMethod($method, ['123'], ['equal' => 123]);
        $this->testMethod($method, [12.0], ['equal' => 12]);


        $stringType = new Core\StringType(['required' => true]);
        $method     = [$intType, "sanitize"];
        $this->testMethod($method, [12], ['equal' => 12]);
        $this->testMethod($method, [null], ['equal' => null]);
        $this->testMethod($method, ['123'], ['equal' => 123]);
        $this->testMethod($method, [12.0], ['equal' => 12]);
    }
}
