<?php

namespace Webbhuset\Whaskell\Observe;

use Webbhuset\Whaskell\WhaskellException;

class SideEffect extends AbstractObserver
{
    protected $function;
    protected $actions;

    public function __construct($function, $actions)
    {
        parent::__construct($function);

        if (is_array($actions)) {
            foreach ($actions as $name => $action) {
                if (!is_callable($action)) {
                    throw new WhaskellException("Observer method '{$name}' in array is not callable");
                }
            }
        }

        $this->actions = $actions;
    }

    public function observeSideEffect($name, $item, $data)
    {
        $function = $this->getAction($name);
        if ($function) {
            $item = $function($item, $data);
        }

        if (isset($this->observer)) {
            $item = $this->observer->observeSideEffect($name, $item, $data);
        }

        return $item;
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

        return false;
    }
}
