<?php

namespace Webbhuset\Bifrost\Core\Component\Transform;

use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Webbhuset\Bifrost\Core\BifrostException;
use Webbhuset\Bifrost\Core\Helper\ReflectionHelper;
use Webbhuset\Bifrost\Core\Monad\Action;

class Reduce implements ComponentInterface
{
    protected $callback;
    protected $initialValue;
    protected $carry;

    public function __construct($callback, $initialValue)
    {
        if (!is_callable($callback)) {
            throw new BifrostException('Callback parameter is not callable');
        }

        $this->validateCallback($callback);

        $this->callback     = $callback;
        $this->initialValue = $initialValue;
        $this->carry        = $initialValue;
    }

    public function process($items, $finalize = true)
    {
        $newItems = [];

        foreach ($items as $key => $item) {
            if (is_string($key)) {
                yield $key => $item;
                continue;
            }
            $this->carry = call_user_func_array($this->callback, [$this->carry, $item]);
        }

        if ($finalize && $this->carry !== $this->initialValue) {
            yield $this->carry;
        }
    }

    protected function validateCallback($callback)
    {
        $reflection = ReflectionHelper::getReflectionFromCallback($callback);

        if (!$reflection) {
            throw new BifrostException('Could not create reflection from callback parameter');
        }

        $params = $reflection->getParameters();

        if (count($params) < 2) {
            throw new BifrostException('The callback requires 3 params. function($carry, $item, $isDone)');
        }
        if (count($params) > 2) {
            foreach ($params as $idx => $param) {
                if ($idx <= 1) {
                    continue;
                }
                if (!$param->isOptional()) {
                    throw new BifrostException("Callback function param {$idx} is not optional. All params except first has to be optional.");
                }
            }
        }
    }
}
