<?php

namespace Webbhuset\Bifrost\Core\Component\Transform;

use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Webbhuset\Bifrost\Core\BifrostException;
use Webbhuset\Bifrost\Core\Monad\Action;

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
        foreach ($items as $key => $item) {
            if (is_string($key)) {
                yield $key => $item;
                continue;
            }
            $this->batch = array_merge($this->batch, [$item]);
            $newItems = $this->processor->process([$item], false);
            $idx = 0;
            foreach ($newItems as $key => $newItem) {
                if (is_string($key)) {
                    yield $key => $newItem;
                    continue;
                }
                if (!isset($this->batch[$idx])) {
                    throw new BifrostException('Merge processor items missmatch. To many items were generated.');
                }
                yield $this->merge($this->batch[$idx], $newItem);
                unset($this->batch[$idx]);
                $idx++;
            }
        }

        if ($finalize && count($this->batch)) {
            $this->batch = array_values($this->batch);
            $newItems = $this->processor->process([], true);
            $idx = 0;
            foreach ($newItems as $key => $newItem) {
                if (is_string($key)) {
                    yield $key => $newItem;
                    continue;
                }
                if (!isset($this->batch[$idx])) {
                    throw new BifrostException('Merge processor items missmatch. To many items were generated.');
                }
                yield $this->merge($this->batch[$idx], $newItem);
                unset($this->batch[$idx]);
                $idx++;
            }
            if (count($this->batch)) {
                throw new BifrostException('Merge processor items missmatch. To few items were generated.');
            }
        }

        return;
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
