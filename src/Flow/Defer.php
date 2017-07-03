<?php

namespace Webbhuset\Whaskell\Flow;

use Webbhuset\Whaskell\WhaskellException;

class Defer
{
    protected $allback;
    protected $args = [];

    public function __construct($callback)
    {
        $args = func_get_args();
        array_shift($args);
        $this->callback = $callback;
        $this->args = $args;
    }

    public function __invoke($items, $finalize = true)
    {
        $function = call_user_func_array($this->callback, $this->args);

        return $function($items, $finalize);
    }
}
