<?php

namespace Webbhuset\Whaskell\Flow;

use Webbhuset\Whaskell\Constructor as F;
use Webbhuset\Whaskell\FunctionInterface;
use Webbhuset\Whaskell\FunctionSignature;
use Webbhuset\Whaskell\WhaskellException;

class Factory implements FunctionInterface
{
    protected $callback;


    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function __invoke($values, $finalize = true)
    {
        $function = call_user_func($this->callback, $value);

        if (is_array($function)) {
            $function = F::Compose($function);
        }

        if (!$function instanceof FunctionInterface) {
            throw new WhaskellException('Function must implement FunctionInterface.');
        }

        return $function($values, $finalize);
    }
}
