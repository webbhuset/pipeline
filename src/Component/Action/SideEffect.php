<?php

namespace Webbhuset\Bifrost\Core\Component\Action;

use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Webbhuset\Bifrost\Core\Data;

class SideEffect implements ComponentInterface
{
    protected $id;
    protected $bind;
    protected $useCallback = false;
    protected $callback;

    public function __construct($idOrCallable, $id = null)
    {
        $args = func_get_args();

        $argOne = array_shift($args);

        if (is_callable($argOne)) {
            $this->callback     = $argOne;
            $this->useCallback  = true;
            $id                 = array_shift($args);
        } else {
            $id = $argOne;
        }

        $this->id       = $id;
        $this->bind     = $args;
    }

    public function process($items, $finalize = true)
    {
        foreach ($items as $key => $item) {
            if (is_string($key)) {
                yield $key => $item;
                continue;
            }
            if (!$this->useCallback || call_user_func($this->callback, $item)) {
                $transport = new Data\Reference($item, $this->bind);
                yield $this->id => $transport;
                yield $transport->data[0];
            } else {
                yield $item;
            }
        }
    }
}

