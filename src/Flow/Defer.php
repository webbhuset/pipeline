<?php

namespace Webbhuset\Whaskell\Flow;

use Webbhuset\Whaskell\AbstractFunction;
use Webbhuset\Whaskell\Constructor as F;
use Webbhuset\Whaskell\FunctionInterface;
use Webbhuset\Whaskell\WhaskellException;

class Defer extends AbstractFunction
{
    protected $callback;
    protected $args = [];
    protected $function;

    public function __construct($callback)
    {
        $args = func_get_args();
        array_shift($args);
        $this->callback = $callback;
        $this->args = $args;
    }

    protected function invoke($items, $finalize = true)
    {
        if (!$this->function) {
            $function = call_user_func_array($this->callback, $this->args);

            if (is_array($function)) {
                $function = F::Compose($function);
            }

            if (!$function instanceof FunctionInterface) {
                throw new WhaskellException('Function must implement FunctionInterface.');
            }

            if ($this->observer) {
                $function->registerObserver($this->observer);
            }

            $this->function = $function;
        } else {
            $function = $this->function;
        }

        return $function($items, $finalize);
    }
}
