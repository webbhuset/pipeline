<?php

namespace Webbhuset\Bifrost\Core\Component\Action;

use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Webbhuset\Bifrost\Core\Data\ActionData\ActionDataInterface;
use Webbhuset\Bifrost\Core\Data\ActionData\EventData;

class Event implements ComponentInterface
{
    protected $name;
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
            $name               = array_shift($args);
        } else {
            $name               = $argOne;
        }

        $this->name = $name;
    }

    public function process($items, $finalize = true)
    {
        foreach ($items as $item) {
            if ($item instanceof ActionDataInterface) {
                yield $item;
                continue;
            }
            if (!$this->useCallback || call_user_func($this->callback, $item)) {
                yield new EventData($this->name, $item);
            }
            yield $item;
        }
    }
}

