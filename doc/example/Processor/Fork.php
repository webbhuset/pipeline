<?php

namespace Webbhuset\Bifrost\Core\Processor;

class Fork
{
    protected $forks;

    public function __construct($forks)
    {
        $this->forks = $forks;
    }

    public function process($items)
    {
        while ($item = $items->current()) {
            foreach ($this->forks as $fork) {
                foreach ($fork->process([$item]) as $res) {
                    yield $res;
                }
            }
            $items->next();
        }
    }

    public function finalize($items = [])
    {
        foreach ($this->forks as $processor) {
            $items = $processor->finalize($items);
        }

        return $items;
    }
}
