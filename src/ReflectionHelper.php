<?php

namespace Webbhuset\Whaskell;

use ReflectionMethod;
use ReflectionFunction;
use Exception;

class ReflectionHelper
{
    public static function getReflectionFromCallback($callback)
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
