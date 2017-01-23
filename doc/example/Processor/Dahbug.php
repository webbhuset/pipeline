<?php

namespace Webbhuset\Bifrost\Core\Processor;

class Dahbug
{
    protected $label;
    public function __construct($label = 'dahbug')
    {
        $this->label = $label;
    }

    public function process($items)
    {
        foreach ($items as $item) {
            \dahbug::dump($item, 'Dahbug');
            yield $item;
        }
    }

    public function finalize($items = [])
    {
        return $items;
    }
}
