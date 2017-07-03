<?php

namespace Webbhuset\Whaskell\Iterable;

use Generator;
use Webbhuset\Whaskell\Dispatch\Data\DataInterface;
use Webbhuset\Whaskell\ReflectionHelper;
use Webbhuset\Whaskell\WhaskellException;

class Expand
{
    protected $callback;

    public function __construct($callback)
    {
        if (!is_callable($callback)) {
            throw new WhaskellException('Callback parameter is not callable.');
        }

        $this->validateCallback($callback);
        $this->callback = $callback;
    }

    public function __invoke($items)
    {
        foreach ($items as $item) {
            if ($item instanceof DataInterface) {
                yield $item;
                continue;
            }
            $generator = call_user_func($this->callback, $item);
            if (!$generator instanceof Generator) {
                throw new WhaskellException('Expand must yield items. Return values are not allowed.');
            }
            foreach ($generator as $yield) {
                yield $yield;
            }
        }
    }

    protected function validateCallback($callback)
    {
        $reflection = ReflectionHelper::getReflectionFromCallback($callback);

        if (!$reflection) {
            throw new WhaskellException('Could not create reflection from callback parameter.');
        }

        $params = $reflection->getParameters();

        if (count($params) < 1) {
            throw new WhaskellException('The callback requires 1 param. function($item)');
        }
        if (count($params) > 1) {
            foreach ($params as $idx => $param) {
                if ($idx == 0) {
                    continue;
                }
                if (!$param->isOptional()) {
                    $idx += 1;
                    throw new WhaskellException("Callback function param {$idx} is not optional. All params except first has to be optional.");
                }
            }
        }
    }
}
