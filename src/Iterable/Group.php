<?php

namespace Webbhuset\Pipeline\Value;

use Webbhuset\Pipeline\FunctionInterface;
use Webbhuset\Pipeline\FunctionSignature;

class Group implements FunctionInterface
{
    protected $callback;
    protected $batch = [];


    public function __construct(callable $callback)
    {
        $canBeUsed = FunctionSignature::canBeUsedWithArgCount($callback, 3, false);

        if ($canBeUsed !== true) {
            throw new \InvalidArgumentException($canBeUsed . ' e.g. function($value, $batch, $finalize)');
        }

        $this->callback = $callback;
    }

    public function __invoke($values, $keepState = false)
    {
        foreach ($values as $value) {
            if (empty($this->batch)) {
                $this->batch[] = $value;

                continue;
            }

            $addToCurrentBatch = call_user_func($this->callback, $value, $this->batch, false);
            if (!$addToCurrentBatch) {
                yield $this->batch;

                $this->batch = [];
            }

            $this->batch[] = $value;
        }

        if (!$keepState
            && call_user_func($this->callback, null, $this->batch, true)
            && count($this->batch)
        ) {
            yield $this->batch;

            $this->batch = [];
        }
    }
}
