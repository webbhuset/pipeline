<?php

namespace Webbhuset\Whaskell\Iterable;

use Webbhuset\Whaskell\Dispatch\Data\DataInterface;
use Webbhuset\Whaskell\WhaskellException;
use Webbhuset\Whaskell\FunctionSignature;

class Expand
{
    protected $callback;

    public function __construct($callback)
    {
        $canBeUsed = FunctionSignature::canBeUsedWithArgCount($callback, 1, true);

        if ($canBeUsed !== true) {
            throw new WhaskellException($canBeUsed . ' Eg. function($item)');
        }

        $this->callback = $callback;
    }

    public function __invoke($items)
    {
        foreach ($items as $item) {
            if ($item instanceof DataInterface) {
                yield $item;
                continue;
            }
            yield from ($this->callback)($item);
        }
    }
}
