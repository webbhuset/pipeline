<?php
namespace Webbhuset\Bifrost\Test;

class Tester
{
    public $subject;
    public $method;
    public $args;
    public $testCase;

    public function __construct($subject, $method, $args, $testCase)
    {
        $this->subject  = $subject;
        $this->method   = $method;
        $this->args     = $args;
        $this->testCase = $testCase;
    }

    public function testThatArgs()
    {
        $args = func_get_args();
        $this->args = $args;

        return $this;
    }

    public function returns($expectedResult)
    {
        $actualResult = $this->getResults();

        if ($expectedResult !== $actualResult) {
            $this->testFailed($expectedResult, $actualResult);
        }

        return $this;
    }

    public function notReturns($expectedResult)
    {
        $actualResult = $this->getResults();

        if ($expectedResult === $actualResult) {
            $this->testFailed($expectedResult, $actualResult);
        }

        return $this;
    }

    public function returnsType($expectedResult)
    {
        $result = $this->getResults();
        $actualResult = gettype($result);

        if ($expectedResult !== $actualResult) {
            $this->testFailed($expectedResult, $actualResult, $result);
        }

        return $this;
    }

    protected function testFailed($excpected, $actual, $value = null)
    {
        $backtrace = debug_backtrace(null, 2);
        $result['test_subject'] = $this->subject;
        $result['test_method']  = $this->method;
        $result['test_args']    = $this->args;
        $result['excpected']    = $excpected;
        $result['actual']       = $actual;
        $result['value']        = $value;
        $result['backtrace']    = $backtrace[1];

        $this->testCase->addError($result);
    }

    protected function getResults()
    {
        return call_user_func_array([$this->subject, $this->method], $this->args);
    }
}
