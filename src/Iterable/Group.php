<?php

namespace Webbhuset\Whaskell\Iterable;

use Webbhuset\Whaskell\AbstractFunction;
use Webbhuset\Whaskell\FunctionSignature;
use Webbhuset\Whaskell\WhaskellException;

class Group extends AbstractFunction
{
    protected $callback;
    protected $batchSize;
    protected $batch = [];

    public function __construct($arg, $shouldMerge = false)
    {
        $this->merge = $shouldMerge;
        if (is_int($arg)) {
            if ($arg < 2 ) {
                throw new WhaskellException('Batch Size has to be larger than 1');
            }
            $this->batchSize = $arg;
            $this->callback = [$this, 'checkBatchSize'];
        } elseif (is_callable($arg)) {

            $canBeUsed = FunctionSignature::canBeUsedWithArgCount($arg, 3, false);

            if ($canBeUsed !== true) {
                throw new WhaskellException($canBeUsed . ' Eg. function($batch, $item, $finalize)');
            }
            $this->callback = $arg;
        } else {
            throw new WhaskellException('Group by callback or batchSize.');
        }
    }

    protected function invoke($items, $shouldFinalize = true)
    {
        foreach ($items as $item) {
            if (empty($this->batch)) {
                $this->addToBatch($item);
                continue;
            }
            $results = call_user_func($this->callback, $this->batch, $item, false);
            if ($results) {
                yield $this->batch;
                $this->batch = [];
            }
            $this->addToBatch($item);
        }

        if (
            $shouldFinalize
            && call_user_func($this->callback, $this->batch, null, true)
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
