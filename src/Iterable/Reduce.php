<?php

namespace Webbhuset\Whaskell\Iterable;

use Webbhuset\Whaskell\WhaskellException;
use Webbhuset\Whaskell\Dispatch\Data\DataInterface;
use Webbhuset\Whaskell\Args;

class Reduce
{
    protected $callback;
    protected $initialValue;
    protected $carry;

    public function __construct($callback, $initialValue)
    {
        $canBeUsed = Args::canBeUsedWithArgCount($callback, 3, false);

        if ($canBeUsed !== true) {
            throw new WhaskellException($canBeUsed . ' Eg. function($carry, $item, $isDone)');
        }

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
}
