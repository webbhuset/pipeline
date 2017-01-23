<?php

namespace Webbhuset\Bifrost\Core\Processor;

use Generator;

class Filter
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

            if ($results) {
                yield $item;
            }
        }
    }

    public function finalize($items = [])
    {
        return $items;
    }
}
