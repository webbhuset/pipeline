<?php

namespace Webbhuset\Bifrost\Helper;

use Webbhuset\Bifrost\BifrostException;

/**
 * Argument parser.
 *
 * @author    Webbhuset AB <info@webbhuset.se>
 * @copyright Copyright (C) 2017 Webbhuset AB
 */
class ArgsParser
{
    /**
     * Parses an argument array.
     *
     * Explodes short args:
     * ['-abc hello']
     * becomes
     * ['a' => true, 'b' => true, 'c' => 'hello'].
     *
     * Replaces aliases:
     * ['-h'] with $aliases = ['h' => 'help']
     * becomes
     * ['help' => true]
     *
     * Merges multiple occurences of same arg:
     * ['--help foo', '--help bar']
     * becomes
     * ['help' => ['foo', 'bar']]
     *
     * @param array $args
     * @param array $aliases
     *
     * @return array
     */
    public static function parseArgs($args, $aliases = [])
    {
        $current = null;
        $parsedArgs = [];

        foreach ($args as $arg) {
            $match = [];
            if (preg_match('/^--([\w\d_-]+)$/', $arg, $match)) {
                $current = $match[1];
                if (!isset($parsedArgs[$current])) {
                    $parsedArgs[$current] = true;
                }

                continue;
            }

            if (preg_match('/^-([\w\d_]+)$/', $arg, $match)) {
                $split = str_split($match[1]);
                foreach ($split as $char) {
                    foreach ($aliases as $alias => $command) {
                        if ($char == $alias) {
                            $char = $command;
                            break;
                        }
                    }
                    $current = $char;
                    if (!isset($parsedArgs[$current])) {
                        $parsedArgs[$current] = true;
                    }
                }

                continue;
            }

            if ($current) {
                if (is_bool($parsedArgs[$current])) {
                    $parsedArgs[$current] = $arg;

                    continue;
                }

                if (is_string($parsedArgs[$current])) {
                    $parsedArgs[$current] = [$parsedArgs[$current]];
                }

                $parsedArgs[$current][] = $arg;
            } else {
                throw new BifrostException("Unexpected argument '{$arg}'. Did you mean '--{$arg}'?\n");
            }
        }

        return $parsedArgs;
    }
}
