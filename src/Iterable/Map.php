<?php

namespace Webbhuset\Pipeline\Iterable;

use Webbhuset\Pipeline\FunctionInterface;
use Webbhuset\Pipeline\FunctionSignature;
use Webbhuset\Pipeline\PipelineException;

class Map implements FunctionInterface
{
    protected $callback;


    public function __construct(callable $callback)
    {
        $canBeUsed = FunctionSignature::canBeUsedWithArgCount($callback, 1, false);

        if ($canBeUsed !== true) {
            throw new PipelineException($canBeUsed . ' e.g. function($value)');
        }

        $this->callback = $callback;
    }

    public function __invoke($values, $keepState = false)
    {
        foreach ($values as $value) {
            $results = call_user_func($this->callback, $value);

            yield $results;
        }
    }
}
