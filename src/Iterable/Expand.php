<?php

namespace Webbhuset\Whaskell\Iterable;

use Webbhuset\Whaskell\FunctionInterface;
use Webbhuset\Whaskell\FunctionSignature;
use Webbhuset\Whaskell\WhaskellException;

class Expand implements FunctionInterface
{
    protected $callback;

    public function __construct(callable $callback = null)
    {
        if ($callback !== null) {
            $canBeUsed = FunctionSignature::canBeUsedWithArgCount($callback, 1, true);

            if ($canBeUsed !== true) {
                throw new WhaskellException($canBeUsed . ' e.g. function($value)');
            }
        } else {
            $callback = function($values) {
                foreach ($values as $value) {
                    yield $value;
                }
            };
        }

        $this->callback = $callback;
    }

    public function __invoke($values, $keepState = false)
    {
        foreach ($values as $value) {
            $generator = call_user_func($this->callback, $value);

            foreach ($generator as $yield) {
                yield $yield;
            }
        }
    }
}
