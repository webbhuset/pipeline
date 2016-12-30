<?php
namespace Webbhuset\Bifrost\Core\Utils;
use \Webbhuset\Bifrost\Core\BifrostException;

/**
 * Parses bridge specifications.
 *
 * @author    Webbhuset AB <info@webbhuset.se>
 * @copyright Copyright (C) 2016 Webbhuset AB
 */
class BridgeSpecificationParser
{
    /**
     * Parses a bridge specification string.
     *
     * @param string $string
     *
     * @return array
     */
    public static function parse($string)
    {
        $rows = explode("\n", $string);
        $rows = array_filter($rows, function($val) {
            return trim($val);
        });

        $baseSpaces = strlen(reset($rows)) - strlen(ltrim(reset($rows)));
        if ($baseSpaces % 4 != 0) {
            throw new BifrostException("Unexpected indentation: Must be divisible by 4, found {$baseSpaces}");
        }

        $levels = [];
        $previousLevel = null;

        foreach (array_reverse($rows) as $row) {
            $spaces = strlen($row) - strlen(ltrim($row)) - $baseSpaces;
            if ($spaces % 4 != 0) {
                throw new BifrostException("Unexpected indentation: Must be divisible by 4, found {$spaces}");
            }
            $level = $spaces / 4;

            if (isset($levels[$level][ltrim($row)])) {
                throw new BifrostException("Duplicate row on same level.");
            }
            if ($previousLevel !== null && $level < $previousLevel) {
                $levels[$level][ltrim($row)] = $levels[$previousLevel];
                unset($levels[$previousLevel]);
            } else {
                $levels[$level][ltrim($row)] = [];
            }
            $previousLevel = $level;
        }

        if (count($levels) != 1) {
            throw new BifrostException('Multiple root levels.');
        }

        $array = self::parseRowArray(reset($levels));

        return reset($array);
    }

    /**
     * Explodes array recursively.
     *
     * @param array $array
     *
     * @return array
     */
    protected static function parseRowArray($array)
    {
        $returnArray = [];

        foreach ($array as $key => $value) {
            $keyArray       = array_map('trim', explode('->', $key));
            $previousPart   = self::parseRowArray($value);

            foreach (array_reverse($keyArray) as $part) {
                if (!$part) {
                    continue;
                }

                if ($previousPart && !self::isArrayKeyInt($previousPart)) {
                    $previousPart = [$previousPart];
                }

                $previousPart = [
                    'name'      => $part,
                    'children'  => $previousPart,
                ];
            }
            array_unshift($returnArray, $previousPart);
        }

        return $returnArray;
    }

    protected static function isArrayKeyInt($array)
    {
        $firstKey = array_keys($array)[0];
        return is_int($firstKey);
    }
}
