<?php

namespace Webbhuset\Bifrost\Processor;

use Generator;
use ArrayObject;

class Reduce
{
    protected $callback;
    protected $initialValue;
    protected $carry;

    public function __construct($callback, $initialValue)
    {
        $this->callback = $callback;
        $this->initialValue = $initialValue;
        $this->carry = new ArrayObject($this->initialValue);
    }

    public function process($items)
    {
        $newItems = [];

        foreach ($items as $item) {
            $yield = call_user_func_array($this->callback, [$this->carry, $item, false]);

            if ($yield->current()) {
                foreach ($yield as $result) {
                    yield $this->carry->getArrayCopy();
                }
                $this->carry = new ArrayObject($this->initialValue);
            }
        }
    }

    public function finalize($items = [])
    {
        $yield = call_user_func_array($this->callback, [$this->carry, null, true]);
        if ($yield->current()) {
            foreach ($yield as $result) {
                yield $this->carry->getArrayCopy();
            }
        }
        $this->carry = new ArrayObject($this->initialValue);
    }
}
