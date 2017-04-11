<?php

namespace Webbhuset\Bifrost\Component\Transform;

use Webbhuset\Bifrost\BifrostException;
use Webbhuset\Bifrost\Component\ComponentInterface;
use Webbhuset\Bifrost\Data\ActionData\ActionDataInterface;

class Merge implements ComponentInterface
{
    protected $processors;
    protected $batch = [];

    public function __construct(ComponentInterface $processor)
    {
        $this->processor = $processor;
    }

    public function process($items, $finalize = true)
    {
        foreach ($items as $item) {
            if ($item instanceof ActionDataInterface) {
                yield $item;
                continue;
            }
            $this->batch    = array_merge($this->batch, [$item]);
            $newItems       = $this->processor->process([$item], false);
            $idx            = 0;
            foreach ($newItems as $newItem) {
                if ($newItem instanceof ActionDataInterface) {
                    yield $newItem;
                    continue;
                }
                if (!isset($this->batch[$idx])) {
                    throw new BifrostException('Merge processor items mismatch. Too many items were generated.');
                }
                yield $this->merge($this->batch[$idx], $newItem);
                unset($this->batch[$idx]);
                $idx++;
            }
        }

        if ($finalize && count($this->batch)) {
            $this->batch    = array_values($this->batch);
            $newItems       = $this->processor->process([], true);
            $idx            = 0;
            foreach ($newItems as $newItem) {
                if ($newItem instanceof ActionDataInterface) {
                    yield $newItem;
                    continue;
                }
                if (!isset($this->batch[$idx])) {
                    throw new BifrostException('Merge processor items mismatch. Too many items were generated.');
                }
                yield $this->merge($this->batch[$idx], $newItem);
                unset($this->batch[$idx]);
                $idx++;
            }
            if (count($this->batch)) {
                throw new BifrostException('Merge processor items mismatch. Too few items were generated.');
            }
        }
    }

    protected function merge($a, $b)
    {
        if (is_array($b)) {
            foreach ($b as $key => $value) {
                if (isset($a[$key])) {
                    if (is_array($value)) {
                        $a[$key] = $this->merge($a[$key], $value);
                    }
                } else {
                    $a[$key] = $value;
                }
            }
        }

        return $a;
    }
}
