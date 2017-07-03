<?php

namespace Webbhuset\Whaskell\Test\UnitTest;

use Webbhuset\Whaskell\Args;

class ArgsTest
{
    public static function canBeUsedWithArgCountTest($test)
    {
        $argsTestMethod         = [(new ArgsTestDummy), 'twoArgs'];
        $argsTestStaticString   = __NAMESPACE__.'\\ArgsTestDummy::twoArgs';
        $argsTestStaticArray    = [__NAMESPACE__.'\\ArgsTestDummy', 'twoArgs'];
        $argsTestFunction       = __NAMESPACE__.'\\ArgsTestFunctionTwoArgsDummy';
        $argsTestClosure        = function($arg1, $arg2, $arg3 = null) {
            return null;
        };
        $generatorFunction      = function($arg1, $arg2 = null, $arg3 = null) {
            yield 1;
        };

        $test
            ->testThatArgs('asdf', 1)->returnsString()

            ->testThatArgs($argsTestClosure, 1)->returnsString()
            ->testThatArgs($argsTestClosure, 2)->returnsTrue()
            ->testThatArgs($argsTestClosure, 3)->returnsTrue()
            ->testThatArgs($argsTestClosure, 4)->returnsString()

            ->testThatArgs($argsTestMethod, 1)->returnsString()
            ->testThatArgs($argsTestMethod, 2)->returnsTrue()
            ->testThatArgs($argsTestMethod, 3)->returnsTrue()
            ->testThatArgs($argsTestMethod, 4)->returnsString()

            ->testThatArgs($argsTestStaticString, 1)->returnsString()
            ->testThatArgs($argsTestStaticString, 2)->returnsTrue()
            ->testThatArgs($argsTestStaticString, 3)->returnsTrue()
            ->testThatArgs($argsTestStaticString, 4)->returnsString()

            ->testThatArgs($argsTestStaticArray, 1)->returnsString()
            ->testThatArgs($argsTestStaticArray, 2)->returnsTrue()
            ->testThatArgs($argsTestStaticArray, 3)->returnsTrue()
            ->testThatArgs($argsTestStaticArray, 4)->returnsString()

            ->testThatArgs($argsTestFunction, 1)->returnsString()
            ->testThatArgs($argsTestFunction, 2)->returnsTrue()
            ->testThatArgs($argsTestFunction, 3)->returnsTrue()
            ->testThatArgs($argsTestFunction, 4)->returnsString()

            ->testThatArgs($generatorFunction, 1, false)->returnsString()
            ->testThatArgs($generatorFunction, 1, true)->returnsTrue()
            ->testThatArgs($argsTestMethod, 2, true)->returnsString()
        ;
    }
}

class ArgsTestDummy
{
    public static function twoArgs($arg1, $arg2, $arg3 = null)
    {
        return null;
    }
}

function ArgsTestFunctionTwoArgsDummy($arg1, $arg2, $arg3 = null) {
    return null;
}
