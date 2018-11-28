<?php

namespace Webbhuset\Whaskell\Iterable;

use Webbhuset\Whaskell\FunctionInterface;
use Webbhuset\Whaskell\FunctionSignature;
use Webbhuset\Whaskell\WhaskellException;

class GroupCount implements FunctionInterface
{
    protected $size;
    protected $batch = [];


    public function __construct($size)
    {
        if ($size !== 0 && $size < 2) {
            throw new WhaskellException('Batch size has to be larger than 1 (or 0 for infinite).');
        }

        $this->size = $size;
    }

    public function __invoke($values, $keepState = false)
    {
        foreach ($values as $value) {
            if (empty($this->batch)) {
                $this->batch[] = $value;

                continue;
            }

            if ($this->size && count($this->batch) >= $this->size) {
                yield $this->batch;

                $this->batch = [];
            }

            $this->batch[] = $value;
        }

        if (!$keepState && $this->batch) {
            yield $this->batch;

            $this->batch = [];
        }
    }
}
