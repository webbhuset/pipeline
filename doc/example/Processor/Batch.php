<?php

namespace Webbhuset\Bifrost\Core\Processor;

class Batch
{
    protected $batch = [];
    protected $size;

    public function __construct($size)
    {
        $this->size = $size;
    }

    public function process($items)
    {
        while ($item = $items->current()) {
            $this->batch[] = $item;

            if (count($this->batch) >= $this->size) {
                $batch = array_splice($this->batch, 0, $this->size);
                foreach ($batch as $batchItem) {
                    yield $batchItem;
                }
            }
            $items->next();
        }

        foreach ($this->batch as $item) {
            yield $item;
        }

        $this->batch = [];
    }
}
