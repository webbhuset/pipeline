<?php

namespace Webbhuset\Bifrost\Core\Component\Flow;

use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Webbhuset\Bifrost\Core\BifrostException;

class Factory implements ComponentInterface
{
    protected $factoryCallback;
    protected $args = [];

    public function __construct($factoryCallback)
    {
        $args = func_get_args();
        array_shift($args);
        $this->factoryCallback = $factoryCallback;
        $this->args = $args;
    }

    public function process($items, $finalize = true)
    {
        $component = call_user_func_array($this->factoryCallback, $this->args);

        return $component->process($items, $finalize);
    }
}
