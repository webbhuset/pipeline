<?php

namespace Webbhuset\Bifrost\Core\Component\Transform;

use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Generator;
use Webbhuset\Bifrost\Core\BifrostException;
use Webbhuset\Bifrost\Core\Helper\ReflectionHelper;
use Webbhuset\Bifrost\Core\Monad\Action;

class Map implements ComponentInterface
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
        foreach ($items as $key => $item) {
            if (is_string($key)) {
                yield $key => $item;
                continue;
            }
            $results = call_user_func($this->callback, $item);
            if ($results instanceof Generator) {
                throw new BifrostException('Yield is not allowed in a mapper. Only values may be returned.');
            }
            yield $results;
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
