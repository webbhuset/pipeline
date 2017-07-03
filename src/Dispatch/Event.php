<?php

namespace Webbhuset\Whaskell\Dispatch;

use Webbhuset\Whaskell\Dispatch\Data\DataInterface;
use Webbhuset\Whaskell\Dispatch\Data\EventData;

class Event
{
    protected $name;
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
            $name               = array_shift($args);
            $bind               = array_shift($args);
        } else {
            $name               = $argOne;
            $bind               = array_shift($args);
        }

        $this->name = $name;
        $this->bind = $bind;
    }

    public function __invoke($items)
    {
        foreach ($items as $item) {
            if ($item instanceof DataInterface) {
                yield $item;
                continue;
            }
            if (!$this->useCallback || call_user_func($this->callback, $item)) {
                yield new EventData($this->name, $item, $this->bind);
            }
            yield $item;
        }
    }
}

