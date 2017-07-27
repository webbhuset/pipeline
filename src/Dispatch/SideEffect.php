<?php

namespace Webbhuset\Whaskell\Dispatch;

use Generator;
use Webbhuset\Whaskell\AbstractFunction;

class SideEffect extends AbstractFunction
{
    protected $id;
    protected $bind;
    protected $useCallback = false;
    protected $callback;

    public function __construct($idOrCallable, $idOrBind = null, $bind = null)
    {
        $args   = func_get_args();
        $argOne = array_shift($args);

        if (is_callable($argOne)) {
            $this->callback     = $argOne;
            $this->useCallback  = true;
            $id                 = array_shift($args);
            $bind               = array_shift($args);
        } else {
            $id                 = $argOne;
            $bind               = array_shift($args);
        }

        $this->id   = $id;
        $this->bind = $bind;
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
            if (!$this->useCallback || call_user_func($this->callback, $item)) {
                $return = $this->observer->observeSideEffect($this->id, $item, $this->bind);
                if ($return instanceof Generator) {
                    foreach ($return as $returnItem) {
                        yield $returnItem;
                    }
                } else {
                    yield $return;
                }
            } else {
                yield $item;
            }
        }
    }
}

