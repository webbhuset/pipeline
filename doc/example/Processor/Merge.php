<?php

namespace Webbhuset\Bifrost\Processor;

class Merge
{
    protected $processors;

    public function __construct($processors)
    {
        $this->processors = $processors;
    }

    public function process($items, $finalize = false)
    {
        $items = iterator_to_array($items);
        $other = $items;

        foreach ($this->processors as $processor) {
            $other = $processor->process($other, $finalize);
        }
        $o = [];
        foreach ($other as $key => $value) {
            $o[$key] = $value;
        }

        foreach ($items as $item) {
            $item['id'] = $o[$item['sku']]['id'];
            yield $item;
        }
    }

    public function finalize()
    {
        $this->process([], true);
    }
}
