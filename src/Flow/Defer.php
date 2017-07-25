<?php

namespace Webbhuset\Whaskell\Flow;

use Webbhuset\Whaskell\WhaskellException;

class Defer
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

    public function __invoke($items, $finalize = true)
    {
        if (!$this->function) {
            $this->function = call_user_func_array($this->callback, $this->args);
        }

        $function = $this->function;

        return $function($items, $finalize);
    }
}
