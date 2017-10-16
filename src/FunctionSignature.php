<?php

namespace Webbhuset\Whaskell;

use ReflectionMethod;
use ReflectionFunction;
use Exception;

class FunctionSignature
{
    public static function canBeUsedWithArgCount($callable, $count, $generator = null)
    {
        if (!is_callable($callable)) {
            return 'Callback is not callable';
        }

        $ref            = self::getReflectionFromCallback($callable);
        $requiredCount  = $ref->getNumberOfRequiredParameters();
        $optionalCount  = $ref->getNumberOfParameters();
        $isGenerator    = $ref->isGenerator();

        if ($isGenerator && $generator === false) {
            return "You cannot use a generator here.";
        }

        if (!$isGenerator && $generator === true) {
            return "Function must be a generator. Use yield instead of return.";
        }

        if ($requiredCount > $count) {
            return "Too many args. Callback Requires {$requiredCount} arg(s), only {$count} will be passed.";
        }

        if ($optionalCount < $count) {
            return "Too few args in callback. {$count} args will be passed but callback only takes {$optionalCount} args.";
        }

        return true;
    }

    protected static function getReflectionFromCallback($callback)
    {
        $reflection = null;
        if (is_array($callback)) {
            list ($class, $method) = $callback;
            $reflection = new ReflectionMethod($class, $method);
        }

        if (!$reflection) {
            try {
                $reflection = new ReflectionFunction($callback);
            } catch (Exception $e) {
            }
        }

        if (!$reflection) {
            try {
                $reflection = new ReflectionMethod($callback);
            } catch (Exception $e) {
            }
        }

        return $reflection;
    }
}
