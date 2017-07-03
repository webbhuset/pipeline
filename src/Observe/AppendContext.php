<?php

namespace Webbhuset\Whaskell\Observe;

use Webbhuset\Whaskell\Dispatch\Data\EventData;

class AppendContext
{
    protected $context;

    public function __construct($context)
    {
        $this->context = $context;
    }

    public function __invoke($items)
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
