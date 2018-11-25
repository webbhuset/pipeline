<?php

namespace Webbhuset\Whaskell\Iterable;

use Webbhuset\Whaskell\FunctionInterface;
use Webbhuset\Whaskell\FunctionSignature;
use Webbhuset\Whaskell\WhaskellException;

class GroupCount implements FunctionInterface
{
    protected $batchSize;
    protected $batch = [];


    public function __construct($batchSize)
    {
        if ($batchSize !== 0 && $batchSize < 2) {
            throw new WhaskellException('Batch size has to be larger than 1 (or 0 for infinite).');
        }

        $this->batchSize = $batchSize;
    }

    public function __invoke($values, $finalize = true)
    {
        foreach ($values as $value) {
            if (empty($this->batch)) {
                $this->batch[] = $value;

                continue;
            }

            if ($this->batchSize && count($this->batch) >= $this->batchSize) {
                yield $this->batch;

                $this->batch = [];
            }

            $this->batch[] = $value;
        }

        if ($finalize && $this->batch) {
            yield $this->batch;

            $this->batch = [];
        }
    }
}
