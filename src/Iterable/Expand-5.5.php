<?php

namespace Webbhuset\Whaskell\Iterable;

use Webbhuset\Whaskell\AbstractFunction;
use Webbhuset\Whaskell\FunctionSignature;
use Webbhuset\Whaskell\WhaskellException;

class Expand extends AbstractFunction
{
    protected $callback;

    public function __construct($callback = null)
    {
        if ($callback !== null) {
            $canBeUsed = FunctionSignature::canBeUsedWithArgCount($callback, 1, true);

            if ($canBeUsed !== true) {
                throw new WhaskellException($canBeUsed . " e.g. 'function($item)'");
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

    protected function invoke($items, $finalize = true)
    {
        foreach ($items as $item) {
            $generator = call_user_func($this->callback, $item);

            foreach ($generator as $yield) {
                yield $yield;
            }
        }
    }
}
