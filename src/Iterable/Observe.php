<?php

namespace Webbhuset\Whaskell\Iterable;

use Webbhuset\Whaskell\FunctionInterface;
use Webbhuset\Whaskell\FunctionSignature;
use Webbhuset\Whaskell\WhaskellException;

class Observe implements FunctionInterface
{
    protected $callback;


    public function __construct($callback)
    {
        $canBeUsed = FunctionSignature::canBeUsedWithArgCount($callback, 1);

        if ($canBeUsed !== true) {
            throw new WhaskellException($canBeUsed . " e.g. 'function(\$item)'");
        }
    }

    public function __invoke($items, $finalize = true)
    {
        foreach ($items as $item) {
            call_user_func($this->callback, $item);

            yield $item;
        }
    }
}
