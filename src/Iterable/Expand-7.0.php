<?php

namespace Webbhuset\Whaskell\Iterable;

use Webbhuset\Whaskell\AbstractFunction;
use Webbhuset\Whaskell\FunctionSignature;
use Webbhuset\Whaskell\WhaskellException;

class Expand implements FunctionInterface
{
    protected $callback;


    public function __construct($callback = null)
    {
        if ($callback !== null) {
            $canBeUsed = FunctionSignature::canBeUsedWithArgCount($callback, 1, true);

            if ($canBeUsed !== true) {
                throw new WhaskellException($canBeUsed . " e.g. 'function(\$item)'");
            }
        } else {
            $callback = function($items) {
                yield from $items;
            };
        }

        $this->callback = $callback;
    }

    public function __invoke($items, $finalize = true)
    {
        foreach ($items as $item) {
            yield from ($this->callback)($item);
        }
    }
}
