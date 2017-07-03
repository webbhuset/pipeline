<?php

namespace Webbhuset\Whaskell\Iterable;

use Generator;
use Webbhuset\Whaskell\ReflectionHelper;
use Webbhuset\Whaskell\WhaskellException;
use Webbhuset\Whaskell\Dispatch\Data\DataInterface;

class Group
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
            $this->validateCallback($arg);
            $this->callback = $arg;
        } else {
            throw new WhaskellException('Group by callback or batchSize.');
        }
    }

    public function __invoke($items, $shouldFinalize = true)
    {
        foreach ($items as $item) {
            if ($item instanceof DataInterface) {
                yield $item;
                continue;
            }
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

    protected function validateCallback($callback)
    {
        $reflection = ReflectionHelper::getReflectionFromCallback($callback);

        if (!$reflection) {
            throw new WhaskellException('Could not create reflection from callback parameter');
        }

        $params = $reflection->getParameters();

        if (count($params) < 3) {
            throw new WhaskellException('The callback requires 3 params. function($batch, $item, $finalize)');
        }
        if (count($params) > 3) {
            foreach ($params as $idx => $param) {
                if ($idx >= 3) {
                    continue;
                }
                if (!$param->isOptional()) {
                    $idx += 1;
                    throw new WhaskellException("Callback function param {$idx} is not optional. All params except the first three has to be optional.");
                }
            }
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
