<?php

namespace Webbhuset\Pipeline\Iterable;

use Webbhuset\Pipeline\FunctionInterface;
use Webbhuset\Pipeline\FunctionSignature;

class Filter implements FunctionInterface
{
    protected $callback;


    public function __construct(callable $callback = null)
    {
        if ($callback !== null) {
            $canBeUsed = FunctionSignature::canBeUsedWithArgCount($callback, 1, false);

            if ($canBeUsed !== true) {
                throw new \InvalidArgumentException($canBeUsed . ' e.g. function($value): bool');
            }
        } else {
            $callback = function($value) {
                return $value;
            };
        }

        $this->callback = $callback;
    }

    public function __invoke($values, $keepState = false)
    {
        foreach ($values as $value) {
            $result = call_user_func($this->callback, $value);

            if ($result) {
                yield $value;
            }
        }
    }
}
