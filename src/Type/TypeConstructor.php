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

    public static function Any()
    {
        $args       = func_get_args();
        $className  = __NAMESPACE__.'\AnyType';
        $refClass   = new ReflectionClass($className);
        $typeObject = $refClass->newInstanceArgs($args);

        return $typeObject;
    }

    public static function Bool()
    {
        $args       = func_get_args();
        $className  = __NAMESPACE__.'\BoolType';
        $refClass   = new ReflectionClass($className);
        $typeObject = $refClass->newInstanceArgs($args);

        return $typeObject;
    }

    public static function Datetime()
    {
        $args       = func_get_args();
        $className  = __NAMESPACE__.'\StringType\\DatetimeType';
        $refClass   = new ReflectionClass($className);
        $typeObject = $refClass->newInstanceArgs($args);

        return $typeObject;
    }

    public static function Decimal()
    {
        $args       = func_get_args();
        $className  = __NAMESPACE__.'\FloatType\\DecimalType';
        $refClass   = new ReflectionClass($className);
        $typeObject = $refClass->newInstanceArgs($args);

        return $typeObject;
    }

    public static function Enum()
    {
        $args       = func_get_args();
        $className  = __NAMESPACE__.'\EnumType';
        $refClass   = new ReflectionClass($className);
        $typeObject = $refClass->newInstanceArgs($args);

        return $typeObject;
    }

    public static function Float()
    {
        $args       = func_get_args();
        $className  = __NAMESPACE__.'\FloatType';
        $refClass   = new ReflectionClass($className);
        $typeObject = $refClass->newInstanceArgs($args);

        return $typeObject;
    }

    public static function Hashmap()
    {
        $args       = func_get_args();
        $className  = __NAMESPACE__.'\HashmapType';
        $refClass   = new ReflectionClass($className);
        $typeObject = $refClass->newInstanceArgs($args);

        return $typeObject;
    }

    public static function Int()
    {
        $args       = func_get_args();
        $className  = __NAMESPACE__.'\IntType';
        $refClass   = new ReflectionClass($className);
        $typeObject = $refClass->newInstanceArgs($args);

        return $typeObject;
    }

    public static function Scalar()
    {
        $args       = func_get_args();
        $className  = __NAMESPACE__.'\ScalarType';
        $refClass   = new ReflectionClass($className);
        $typeObject = $refClass->newInstanceArgs($args);

        return $typeObject;
    }

    public static function Set()
    {
        $args       = func_get_args();
        $className  = __NAMESPACE__.'\SetType';
        $refClass   = new ReflectionClass($className);
        $typeObject = $refClass->newInstanceArgs($args);

        return $typeObject;
    }

    public static function String()
    {
        $args       = func_get_args();
        $className  = __NAMESPACE__.'\StringType';
        $refClass   = new ReflectionClass($className);
        $typeObject = $refClass->newInstanceArgs($args);

        return $typeObject;
    }

    public static function Struct()
    {
        $args       = func_get_args();
        $className  = __NAMESPACE__.'\StructType';
        $refClass   = new ReflectionClass($className);
        $typeObject = $refClass->newInstanceArgs($args);

        return $typeObject;
    }

    public static function Union()
    {
        $args       = func_get_args();
        $className  = __NAMESPACE__.'\UnionType';
        $refClass   = new ReflectionClass($className);
        $typeObject = $refClass->newInstanceArgs($args);

        return $typeObject;
    }
}
