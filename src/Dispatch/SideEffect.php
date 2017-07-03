<?php

namespace Webbhuset\Whaskell\Dispatch;

use Generator;
use Webbhuset\Whaskell\Dispatch\Data\DataInterface;
use Webbhuset\Whaskell\Dispatch\Data\SideEffectData;

class SideEffect
{
    protected $id;
    protected $bind;
    protected $useCallback = false;
    protected $callback;

    public function __construct($idOrCallable, $id = null)
    {
        $args   = func_get_args();
        $argOne = array_shift($args);

        if (is_callable($argOne)) {
            $this->callback     = $argOne;
            $this->useCallback  = true;
            $id                 = array_shift($args);
        } else {
            $id                 = $argOne;
        }

        $this->id       = $id;
        $this->bind     = $args;
    }

    public function __invoke($items, $finalize = true)
    {
        foreach ($items as $key => $item) {
            if ($item instanceof DataInterface) {
                yield $item;
                continue;
            }
            if (!$this->useCallback || call_user_func($this->callback, $item)) {
                $data = new SideEffectData($this->id, $item, $this->bind);
                yield $data;

                if ($data->getItem() instanceof Generator) {
                    foreach ($data->getItem() as $dataItem) {
                        yield $dataItem;
                    }
                } else {
                    yield $data->getItem();
                }
            } else {
                yield $item;
            }
        }
    }
}

