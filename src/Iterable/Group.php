<?php

namespace Webbhuset\Whaskell\Iterable;

use Webbhuset\Whaskell\FunctionInterface;
use Webbhuset\Whaskell\FunctionSignature;
use Webbhuset\Whaskell\WhaskellException;

class Group implements FunctionInterface
{
    protected $callback;
    protected $batchSize;
    protected $batch = [];


    public function __construct($arg, $shouldMerge = false)
    {
        $this->merge = $shouldMerge;

        if (is_int($arg)) {
            if ($arg < 2) {
                throw new WhaskellException('Batch Size has to be larger than 1');
            }

            $this->batchSize = $arg;
            $this->callback = [$this, 'checkBatchSize'];
        } elseif (is_callable($arg)) {
            $canBeUsed = FunctionSignature::canBeUsedWithArgCount($arg, 3, false);

            if ($canBeUsed !== true) {
                throw new WhaskellException($canBeUsed . ' e.g. function($item, $batch, $finalize)');
            }

            $this->callback = $arg;
        } else {
            throw new WhaskellException('Group by callback or batchSize.');
        }
    }

    public function __invoke($items, $finalize = true)
    {
        foreach ($items as $item) {
            if (empty($this->batch)) {
                $this->addToBatch($item);

                continue;
            }

            $addToCurrentBatch = call_user_func($this->callback, $item, $this->batch, false);
            if (!$addToCurrentBatch) {
                yield $this->batch;

                $this->batch = [];
            }

            $this->addToBatch($item);
        }

        if (
            $finalize
            && call_user_func($this->callback, null, $this->batch, true)
            && count($this->batch) > 0
        ) {
            yield $this->batch;

            $this->batch = [];
        }
    }

    protected function addToBatch($item)
    {
        if ($this->merge) {
            foreach ($item as $key => $value) {
                $this->batch[$key] = $value;
            }
        } else {
            $this->batch[] = $item;
        }
    }

    protected function checkBatchSize($batch, $item, $finalize)
    {
        if ($finalize) {
            return true;
        }

        return count($batch) >= $this->batchSize;
    }
}
