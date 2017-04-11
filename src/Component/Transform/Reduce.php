<?php

namespace Webbhuset\Bifrost\Component\Transform;

use Webbhuset\Bifrost\BifrostException;
use Webbhuset\Bifrost\Component\ComponentInterface;
use Webbhuset\Bifrost\Data\ActionData\ActionDataInterface;
use Webbhuset\Bifrost\Helper\ReflectionHelper;

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

        foreach ($items as $item) {
            if ($item instanceof ActionDataInterface) {
                yield $item;
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
