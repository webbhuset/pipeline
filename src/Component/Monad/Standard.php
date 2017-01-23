<?php

namespace Webbhuset\Bifrost\Core\Component\Monad;

use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Webbhuset\Bifrost\Core\BifrostException;

class Standard implements ComponentInterface
{
    protected $actions;
    protected $passthru;

    public function __construct($actions, $passthru = false)
    {
        if (is_array($actions)) {
            foreach ($actions as $name => $action) {
                if (!is_callable($action)) {
                    throw new BifrostException("Monad method '{$name}' in array is not callable");
                }
            }
        }
        $this->passthru = $passthru;
        $this->actions  = $actions;
    }

    public function process($items, $finalize = true)
    {
        foreach ($items as $key => $item) {
            if (is_string($key)) {
                $action = $this->getAction($key);
                if ($action) {
                    $result = call_user_func_array($action, array_merge([$item->data[0]], $item->data[1]));
                    if ($result) {
                        $item->data[0] = $result;
                    }
                    continue;
                } else {
                    yield $key => $item;
                }
            }
            if ($this->passthru) {
                yield $key => $item;
            }
        }
    }

    protected function getAction($name)
    {
        if (is_array($this->actions) && isset($this->actions[$name])) {
            return $this->actions[$name];
        }

        if (is_object($this->actions)) {
            $method = [$this->actions, $name];
            if (is_callable($method)) {
                return $method;
            }
        }
    }
}
