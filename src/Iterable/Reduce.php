<?php

namespace Webbhuset\Whaskell\Iterable;

use Webbhuset\Whaskell\WhaskellException;
use Webbhuset\Whaskell\Dispatch\Data\DataInterface;
use Webbhuset\Whaskell\ReflectionHelper;

class Reduce
{
    protected $callback;
    protected $initialValue;
    protected $carry;

    public function __construct($callback, $initialValue)
    {
        if (!is_callable($callback)) {
            throw new WhaskellException('Callback parameter is not callable');
        }

        $this->validateCallback($callback);

        $this->callback     = $callback;
        $this->initialValue = $initialValue;
        $this->carry        = $initialValue;
    }

    public function __invoke($items, $finalize = true)
    {
        $newItems = [];

        foreach ($items as $item) {
            if ($item instanceof DataInterface) {
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
            throw new WhaskellException('Could not create reflection from callback parameter');
        }

        $params = $reflection->getParameters();

        if (count($params) < 2) {
            throw new WhaskellException('The callback requires 3 params. function($carry, $item, $isDone)');
        }
        if (count($params) > 2) {
            foreach ($params as $idx => $param) {
                if ($idx <= 1) {
                    continue;
                }
                if (!$param->isOptional()) {
                    throw new WhaskellException("Callback function param {$idx} is not optional. All params except first has to be optional.");
                }
            }
        }
    }
}
