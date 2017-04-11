<?php

namespace Webbhuset\Bifrost\Component\Monad;

use Webbhuset\Bifrost\Component\ComponentInterface;
use Webbhuset\Bifrost\Data\ActionData\EventData;

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
