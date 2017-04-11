<?php

namespace Webbhuset\Bifrost\Component\Action;

use Webbhuset\Bifrost\Component\ComponentInterface;
use Webbhuset\Bifrost\Data\ActionData\ActionDataInterface;
use Webbhuset\Bifrost\Data\ActionData\EventData;

class Event implements ComponentInterface
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

    public function process($items)
    {
        foreach ($items as $item) {
            if ($item instanceof ActionDataInterface) {
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

