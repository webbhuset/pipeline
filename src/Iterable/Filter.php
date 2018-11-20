<?php

namespace Webbhuset\Whaskell\Iterable;

use Webbhuset\Whaskell\FunctionSignature;
use Webbhuset\Whaskell\WhaskellException;

class Filter implements FunctionInterface
{
    protected $callback;


    public function __construct($callback)
    {
        $canBeUsed = FunctionSignature::canBeUsedWithArgCount($callback, 1, false);

        if ($canBeUsed !== true) {
            throw new WhaskellException($canBeUsed . ' e.g. function($item): bool');
        }

        $this->callback = $callback;
    }

    public function __invoke($items, $finalize = true)
    {
        foreach ($items as $item) {
            $results = call_user_func($this->callback, $item);

            if ($results) {
                yield $item;
            }
        }
    }
}
