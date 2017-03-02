<?php

namespace Webbhuset\Bifrost\Core\Component\Monad;

use Webbhuset\Bifrost\Core\BifrostException;
use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Webbhuset\Bifrost\Core\Data\ActionData\SideEffectData;

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
        foreach ($items as $item) {
            if ($item instanceof SideEffectData) {
                $action = $this->getAction($item->getName());
                if ($action) {
                    $result = call_user_func_array($action, array_merge([$item->getItem()], $item->getData()));
                    if ($result) {
                        $item->setItem($result);
                    }
                    if (!$this->passthru) {
                        continue;
                    }
                }
            }
            yield $item;
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
