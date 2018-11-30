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
        if (!is_numeric($size)) {
            throw new \InvalidArgumentException(FunctionSignature::invalidTypeMessage('$size', 'int', $size));
        }

        $size = (int)$size;

        if ($size < 2) {
            throw new \InvalidArgumentException('$size must be larger than 1.');
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

            if (count($this->batch) >= $this->size) {
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
