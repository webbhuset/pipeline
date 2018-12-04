<?php

namespace Webbhuset\Pipeline\Flow;

use Webbhuset\Pipeline\Constructor as F;
use Webbhuset\Pipeline\FunctionInterface;
use Webbhuset\Pipeline\FunctionSignature;
use Webbhuset\Pipeline\PipelineException;

class Factory implements FunctionInterface
{
    protected $callback;


    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function __invoke($values)
    {
        foreach ($values as $value) {
            $function = call_user_func($this->callback, $value);

            if (is_array($function)) {
                $function = F::Compose($function);
            }

            if (!$function instanceof FunctionInterface) {
                throw new PipelineException('Function must implement FunctionInterface.');
            }

            yield $function($values, false);
        }
    }
}
