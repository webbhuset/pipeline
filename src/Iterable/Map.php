<?php

namespace Webbhuset\Whaskell\Iterable;

use Webbhuset\Whaskell\FunctionInterface;
use Webbhuset\Whaskell\FunctionSignature;
use Webbhuset\Whaskell\WhaskellException;

class Map implements FunctionInterface
{
    protected $callback;


    public function __construct(callable $callback)
    {
        $canBeUsed = FunctionSignature::canBeUsedWithArgCount($callback, 1, false);

        if ($canBeUsed !== true) {
            throw new WhaskellException($canBeUsed . ' e.g. function($value)');
        }

        $this->callback = $callback;
    }

    public function __invoke($values, $finalize = true)
    {
        foreach ($values as $value) {
            $results = call_user_func($this->callback, $value);

            yield $results;
        }
    }
}
