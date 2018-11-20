<?php

namespace Webbhuset\Whaskell\Iterable;

use Webbhuset\Whaskell\FunctionInterface;
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
                throw new WhaskellException($canBeUsed . ' e.g. function($item)');
            }
        } else {
            $callback = function($items) {
                foreach ($items as $item) {
                    yield $item;
                }
            };
        }

        $this->callback = $callback;
    }

    public function __invoke($items, $finalize = true)
    {
        foreach ($items as $item) {
            $generator = call_user_func($this->callback, $item);

            foreach ($generator as $yield) {
                yield $yield;
            }
        }
    }
}
