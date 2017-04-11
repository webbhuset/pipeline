<?php

namespace Webbhuset\Bifrost\Processor;

use Generator;

class Map
{
    protected $callback;

    public function __construct($callback)
    {
        $this->callback = $callback;
    }

    public function process($items)
    {
        foreach ($items as $idx => $item) {
            $results = call_user_func($this->callback, $item);
            yield $results;
        }
    }

    public function finalize($items = [])
    {
        return $items;
    }
}
