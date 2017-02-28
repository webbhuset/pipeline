<?php

namespace Webbhuset\Bifrost\Core\Component\Action;

use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Webbhuset\Bifrost\Core\Data;

class Event implements ComponentInterface
{
    protected $id;
    protected $bind;
    protected $useCallback = false;
    protected $callback;

    public function __construct($idOrCallable)
    {
        $args   = func_get_args();
        $argOne = array_shift($args);

        if (is_callable($argOne)) {
            $this->callback     = $argOne;
            $this->useCallback  = true;
            $name               = array_shift($args);
        } else {
            $name               = $argOne;
        }

        $this->id       = 'event';
        $this->bind     = $name;
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
                yield $item;
            } else {
                yield $item;
            }
        }
    }
}

