<?php

namespace Webbhuset\Whaskell\Flow;

use Webbhuset\Whaskell\Constructor as F;
use Webbhuset\Whaskell\FunctionInterface;
use Webbhuset\Whaskell\FunctionSignature;
use Webbhuset\Whaskell\WhaskellException;

class Factory implements FunctionInterface
{
    protected $callback;


    public function __construct($callback)
    {
        $this->callback = $callback;
    }

    public function __invoke($items, $finalize = true)
    {
        $function = call_user_func($this->callback, $item);

        if (is_array($function)) {
            $function = F::Compose($function);
        }

        if (!$function instanceof FunctionInterface) {
            throw new WhaskellException('Function must implement FunctionInterface.');
        }

        return $function($items, $finalize);
    }
}
