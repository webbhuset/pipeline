<?php

namespace Webbhuset\Whaskell\Flow;

use Webbhuset\Whaskell\Constructor as F;
use Webbhuset\Whaskell\FunctionInterface;
use Webbhuset\Whaskell\FunctionSignature;
use Webbhuset\Whaskell\WhaskellException;

class Defer implements FunctionInterface
{
    protected $callback;
    protected $function;


    public function __construct(callable $callback)
    {
        $canBeUsed = FunctionSignature::canBeUsedWithArgCount($callback, 0, false);

        if ($canBeUsed !== true) {
            throw new WhaskellException($canBeUsed . ' e.g. function()');
        }

        $this->callback = $callback;
    }

    public function __invoke($values, $keepState = false)
    {
        if (!$this->function) {
            $function = call_user_func($this->callback);

            if (is_array($function)) {
                $function = F::Compose($function);
            }

            if (!$function instanceof FunctionInterface) {
                throw new WhaskellException('Function must implement FunctionInterface.');
            }

            $this->function = $function;
        } else {
            $function = $this->function;
        }

        return $function($values, $keepState);
    }
}
