<?php

namespace Webbhuset\Bifrost\Component\Transform;

use Generator;
use Webbhuset\Bifrost\BifrostException;
use Webbhuset\Bifrost\Component\ComponentInterface;
use Webbhuset\Bifrost\Data\ActionData\ActionDataInterface;
use Webbhuset\Bifrost\Helper\ReflectionHelper;

class Filter implements ComponentInterface
{
    protected $callback;

    public function __construct($callback)
    {
        if (!is_callable($callback)) {
            throw new BifrostException('Callback parameter is not callable');
        }

        $this->validateCallback($callback);

        $this->callback = $callback;
    }

    public function process($items)
    {
        foreach ($items as $item) {
            if ($item instanceof ActionDataInterface) {
                yield $item;
                continue;
            }
            $results = call_user_func($this->callback, $item);

            if ($results instanceof Generator) {
                throw new BifrostException('Yield is not allowed in a filter. Only bool values may be returned.');
            }

            if ($results) {
                yield $item;
            }
        }
    }

    protected function validateCallback($callback)
    {
        $reflection = ReflectionHelper::getReflectionFromCallback($callback);

        if (!$reflection) {
            throw new BifrostException('Could not create reflection from callback parameter');
        }

        $params = $reflection->getParameters();

        if (count($params) < 1) {
            throw new BifrostException('The callback requires 1 param. function($item)');
        }
        if (count($params) > 1) {
            foreach ($params as $idx => $param) {
                if ($idx == 0) {
                    continue;
                }
                if (!$param->isOptional()) {
                    $idx += 1;
                    throw new BifrostException("Callback function param {$idx} is not optional. All params except first has to be optional.");
                }
            }
        }
    }
}
