<?php

namespace Webbhuset\Bifrost\Test\UnitTest\Type;
use Webbhuset\Bifrost\Type\TypeConstructor AS T;

class TypeConstructorTest
{
    public static function NULLABLETest($test)
    {
        $test
            ->testThatArgs(true)->returnsValue(T::NULLABLE)
            ->testThatArgs(false)->notReturnsValue(T::NULLABLE);
    }

    public static function MINTest($test)
    {
        $test->testThatArgs(12)->returnsValue([T::ARG_KEY_MIN => 12]);
    }

    public static function MAXTest($test)
    {
        $test->testThatArgs(12)->returnsValue([T::ARG_KEY_MAX => 12]);
    }

    public static function MATCHTest($test)
    {
        $test->testThatArgs(['\d' => 'Number'])->returnsValue([T::ARG_KEY_MATCH => ['\d' => 'Number']]);
    }

    public static function NOTMATCHTest($test)
    {
        $test->testThatArgs(['\d' => 'Number'])->returnsValue([T::ARG_KEY_NOTMATCH => ['\d' => 'Number']]);
    }

    public static function AnyTest($test)
    {
        $ns = 'Webbhuset\\Bifrost\\Type\\';
        $test->testThatArgs()->returnsInstanceOf($ns.'AnyType');
    }

    public static function BoolTest($test)
    {
        $ns = 'Webbhuset\\Bifrost\\Type\\';
        $test->testThatArgs()->returnsInstanceOf($ns.'BoolType');
    }

    public static function EnumTest($test)
    {
        $ns = 'Webbhuset\\Bifrost\\Type\\';
        $test->testThatArgs([1])->returnsInstanceOf($ns.'EnumType');
    }

    public static function DecimalTest($test)
    {
        $ns = 'Webbhuset\\Bifrost\\Type\\';
        $test->testThatArgs()->returnsInstanceOf($ns.'FloatType\\DecimalType');
    }

    public static function DatetimeTest($test)
    {
        $ns = 'Webbhuset\\Bifrost\\Type\\';
        $test->testThatArgs()->returnsInstanceOf($ns.'StringType\\DatetimeType');
    }

    public static function FloatTest($test)
    {
        $ns = 'Webbhuset\\Bifrost\\Type\\';
        $test->testThatArgs()->returnsInstanceOf($ns.'FloatType');
    }

    public static function HashmapTest($test)
    {
        $ns = 'Webbhuset\\Bifrost\\Type\\';
        $test->testThatArgs(T::Int(), T::String())->returnsInstanceOf($ns.'HashmapType');
    }

    public static function IntTest($test)
    {
        $ns = 'Webbhuset\\Bifrost\\Type\\';
        $test->testThatArgs()->returnsInstanceOf($ns.'IntType');
    }

    public static function ScalarTest($test)
    {
        $ns = 'Webbhuset\\Bifrost\\Type\\';
        $test->testThatArgs()->returnsInstanceOf($ns.'ScalarType');
    }

    public static function SetTest($test)
    {
        $ns = 'Webbhuset\\Bifrost\\Type\\';
        $test->testThatArgs(T::Int())->returnsInstanceOf($ns.'SetType');
    }

    public static function StringTest($test)
    {
        $ns = 'Webbhuset\\Bifrost\\Type\\';
        $test->testThatArgs()->returnsInstanceOf($ns.'StringType');
    }

    public static function StructTest($test)
    {
        $ns = 'Webbhuset\\Bifrost\\Type\\';
        $test->testThatArgs(['test' => T::Int()])->returnsInstanceOf($ns.'StructType');
    }

    public static function UnionTest($test)
    {
        $ns = 'Webbhuset\\Bifrost\\Type\\';
        $test->testThatArgs()->returnsInstanceOf($ns.'UnionType');
    }
}
