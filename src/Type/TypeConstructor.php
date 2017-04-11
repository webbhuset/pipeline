<?php

namespace Webbhuset\Bifrost\Type;
use ReflectionClass;

class TypeConstructor
{
    const R = 'REQUIRED';

    protected static $map = [
        'Str' => 'String',
        'Map' => 'Hashmap',
    ];

    public static function __callStatic($fn, $args)
    {
        $className      = self::getClassName($fn);

        if (!$className) {
            throw new TypeException("Type {$fn} is not recoginzed", null, null, []);
        }

        $refClass       = new ReflectionClass($className);
        $typeObject     = $refClass->newInstanceArgs($args);

        return $typeObject;
    }

    protected static function getClassName($name)
    {
        $className = '';

        if (preg_match_all('/[A-Z][a-z]+/', $name, $match)) {
            foreach ($match[0] as $part) {
                if (array_key_exists($part, self::$map)) {
                    $part = self::$map[$part];
                }
                $className .= '\\'.$part.'Type';
            }
        }

        if ($className) {
            return __NAMESPACE__.$className;
        }
    }
}

