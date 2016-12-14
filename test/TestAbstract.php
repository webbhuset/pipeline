<?php

namespace Webbhuset\Bifrost\Test;

use ReflectionClass;
use Exception;

abstract class TestAbstract implements TestInterface
{
    protected $subject;
    protected $subjectInstance;
    protected $subjectMethod;
    protected $errors = [];

    public function __construct($subjectClassName)
    {
        if (!class_exists($subjectClassName)) {
            throw new Exception("Class does not exist: {$subjectClassName}");
        }

        $this->subject = $subjectClassName;

    }

    public function newInstance($args = null)
    {
        $args      = func_get_args();
        $reflector = new ReflectionClass($this->subject);
        $instance  = $reflector->newInstanceArgs($args);

        $this->subjectInstance = $instance;

        return $this;
    }

    public function testThatArgs()
    {
        $args = func_get_args();

        $tester = new Tester($this->subjectInstance, $this->subjectMethod, $args, $this);

        return $tester;
    }

    public function run($args)
    {
        $this->runAllInterfaceMethods();

        if ($this->errors) {
        }
    }

    public function addError($result)
    {
        $this->errors[] = $result;
    }

    protected function runAllInterfaceMethods()
    {
        $refClass = new ReflectionClass($this->subject);
        $interfaces = $refClass->getInterfaces();

        foreach ($interfaces as $interface) {
            $methods = $interface->getMethods();

            foreach ($methods as $method) {
                $testMethodName = $method->getName() . 'Test';

                if (!method_exists($this, $testMethodName)) {
                    echo "Method unit test missing: {$this->subject}::{$method->getName()}\n";
                    continue;
                }
                $this->subjectMethod = $method->getName();
                $this->subjectInstance = null;

                $this->{$testMethodName}();
            }
        }
    }

    public function testMethod()
    {
    }
}
