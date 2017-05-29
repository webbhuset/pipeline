<?php

namespace Webbhuset\Bifrost\Type;
use ReflectionClass;

class TypeConstructor
{
    const NULLABLE          = 'IS_NULLABLE';
    const ARG_KEY_MIN       = 'KEY_MIN';
    const ARG_KEY_MAX       = 'KEY_MAX';
    const ARG_KEY_MATCH     = 'KEY_MATCH';
    const ARG_KEY_NOTMATCH  = 'KEY_NOTMATCH';

    protected static $map = [
        'Str' => 'String',
        'Map' => 'Hashmap',
    ];

    public static function NULLABLE($flag)
    {
        if ($flag) {
            return self::NULLABLE;
        }

        return null;
    }

    public static function MIN($arg)
    {
        return [self::ARG_KEY_MIN => $arg];
    }

    public static function MAX($arg)
    {
        return [self::ARG_KEY_MAX => $arg];
    }

    public static function MATCH($arg)
    {
        return [self::ARG_KEY_MATCH => $arg];
    }

    public static function NOTMATCH($arg)
    {
        return [self::ARG_KEY_NOTMATCH => $arg];
    }

    public static function Any(...$args)
    {
        $className  = __NAMESPACE__.'\AnyType';
        $typeObject = new $className(...$args);

        return $typeObject;
    }

    public static function Bool(...$args)
    {
        $className  = __NAMESPACE__.'\BoolType';
        $typeObject = new $className(...$args);

        return $typeObject;
    }

    public static function Datetime(...$args)
    {
        $className  = __NAMESPACE__.'\StringType\\DatetimeType';
        $typeObject = new $className(...$args);

        return $typeObject;
    }

    public static function Decimal(...$args)
    {
        $className  = __NAMESPACE__.'\FloatType\\DecimalType';
        $typeObject = new $className(...$args);

        return $typeObject;
    }

    public static function Enum(...$args)
    {
        $className  = __NAMESPACE__.'\EnumType';
        $typeObject = new $className(...$args);

        return $typeObject;
    }

    public static function Float(...$args)
    {
        $className  = __NAMESPACE__.'\FloatType';
        $typeObject = new $className(...$args);

        return $typeObject;
    }

    public static function Hashmap(...$args)
    {
        $className  = __NAMESPACE__.'\HashmapType';
        $typeObject = new $className(...$args);

        return $typeObject;
    }

    public static function Int(...$args)
    {
        $className  = __NAMESPACE__.'\IntType';
        $typeObject = new $className(...$args);

        return $typeObject;
    }

    public static function Scalar(...$args)
    {
        $className  = __NAMESPACE__.'\ScalarType';
        $typeObject = new $className(...$args);

        return $typeObject;
    }

    public static function Set(...$args)
    {
        $className  = __NAMESPACE__.'\SetType';
        $typeObject = new $className(...$args);

        return $typeObject;
    }

    public static function String(...$args)
    {
        $className  = __NAMESPACE__.'\StringType';
        $typeObject = new $className(...$args);

        return $typeObject;
    }

    public static function Struct(...$args)
    {
        $className  = __NAMESPACE__.'\StructType';
        $typeObject = new $className(...$args);

        return $typeObject;
    }

    public static function Union(...$args)
    {
        $className  = __NAMESPACE__.'\UnionType';
        $typeObject = new $className(...$args);

        return $typeObject;
    }
}
