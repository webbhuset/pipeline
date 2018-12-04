<?php

use Webbhuset\Pipeline\FunctionInterface;
use Webbhuset\Pipeline\FunctionSignature;
use Webbhuset\Pipeline\PipelineException;

class DropWhile implements FunctionInterface
{
    protected $callback;
    protected $done;


    public function __construct(callable $callback)
    {
        $canBeUsed = FunctionSignature::canBeUsedWithArgCount($callback, 1, false);

        if ($canBeUsed !== true) {
            throw new PipelineException($canBeUsed . ' e.g. function($value): bool');
        }

        $this->callback = $callback;
    }

    public function __invoke($values, $keepState = false)
    {
        foreach ($values as $value) {
            if ($this->done) {
                yield $value;

                continue;
            }

            $this->done = !call_user_func($this->callback, $value);

            if ($this->done) {
                yield $value;
            }
        }

        if (!$keepState) {
            $this->done = false;
        }
    }
}
