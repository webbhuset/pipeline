<?php

namespace Webbhuset\Bifrost\Core\Component\Monad;

use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Webbhuset\Bifrost\Core\Data\ActionData\EventData;

class AppendContext implements ComponentInterface
{
    protected $context;

    public function __construct($context)
    {
        $this->context = $context;
    }

    public function process($items)
    {
        foreach ($items as $item) {
            if ($item instanceof EventData) {
                yield $item->appendContext($this->context);
            } else {
                yield $item;
            }

        }
    }
}
