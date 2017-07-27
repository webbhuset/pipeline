<?php

namespace Webbhuset\Whaskell\Dispatch;

use Webbhuset\Whaskell\AbstractFunction;
use Webbhuset\Whaskell\FunctionSignature;
use Webbhuset\Whaskell\WhaskellException;

class Error extends AbstractFunction
{
    protected $callback;

    public function __construct($callback)
    {
        $canBeUsed = FunctionSignature::canBeUsedWithArgCount($callback, 1);

        if ($canBeUsed !== true) {
            throw new WhaskellException($canBeUsed . ' Eg. function($item)');
        }

        $this->callback = $callback;
    }

    protected function invoke($items, $finalize = true)
    {
        if (!$this->observer) {
            foreach ($items as $item) {
                yield $item;
            }

            return;
        }

        foreach ($items as $item) {
            $result = call_user_func($this->callback, $item);
            if ($result) {
                if ($this->observer) {
                    $this->observer->observeEvent('error', $item, $result, []);
                }
            } else {
                yield $item;
            }
        }
    }
}

