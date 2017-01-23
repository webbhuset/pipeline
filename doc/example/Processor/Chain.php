<?php

namespace Webbhuset\Bifrost\Core\Processor;

class Chain
{
    protected $processors;

    public function __construct($processors)
    {
        $this->processors = $processors;
    }

    public function process($items, $isFirst = true)
    {
        foreach ($this->processors as $processor) {
            $items = $processor->process($items);
        }

        return $items;
    }

    public function finalize($items = [])
    {
        foreach ($this->processors as $processor) {
            $items = $processor->finalize($items);
        }

        return $items;
    }
}
