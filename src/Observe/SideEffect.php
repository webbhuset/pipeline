<?php

namespace Webbhuset\Whaskell\Observe;

use Webbhuset\Whaskell\WhaskellException;

class SideEffect extends AbstractObserver
{
    protected $sideEffectFunctions;

    public function __construct($function, $sideEffectFunctions)
    {
        parent::__construct($function);

        if (is_array($sideEffectFunctions)) {
            foreach ($sideEffectFunctions as $name => $sideEffectFunction) {
                if (!is_callable($sideEffectFunction)) {
                    throw new WhaskellException("Observer method '{$name}' in array is not callable");
                }
            }
        }

        $this->sideEffectFunctions = $sideEffectFunctions;
    }

    public function observeSideEffect($name, $item, $data)
    {
        $function = $this->getSideEffectFunction($name);
        if ($function) {
            $item = $function($item, $data);
        }

        if (isset($this->observer)) {
            $item = $this->observer->observeSideEffect($name, $item, $data);
        }

        return $item;
    }

    protected function getSideEffectFunction($name)
    {
        if (is_array($this->sideEffectFunctions) && isset($this->sideEffectFunctions[$name])) {
            return $this->sideEffectFunctions[$name];
        }

        if (is_object($this->sideEffectFunctions)) {
            $method = [$this->sideEffectFunctions, $name];
            if (is_callable($method)) {
                return $method;
            }
        }

        return false;
    }
}
