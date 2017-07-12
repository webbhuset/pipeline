<?php

namespace Webbhuset\Whaskell\Iterable;

use Webbhuset\Whaskell\WhaskellException;
use Webbhuset\Whaskell\Dispatch\Data\DataInterface;

class Merge
{
    protected $function;
    protected $batch = [];

    public function __construct(Callable $function)
    {
        $this->function = $function;
    }

    public function __invoke($items, $finalize = true)
    {
        $function = $this->function;

        foreach ($items as $item) {
            if ($item instanceof DataInterface) {
                yield $item;
                continue;
            }
            $this->batch    = array_merge($this->batch, [$item]);
            $newItems       = $function([$item], false);
            $idx            = 0;
            foreach ($newItems as $newItem) {
                if ($newItem instanceof DataInterface) {
                    yield $newItem;
                    continue;
                }
                if (!isset($this->batch[$idx])) {
                    throw new WhaskellException('Merge function items mismatch. Too many items were generated.');
                }
                yield $this->merge($this->batch[$idx], $newItem);
                unset($this->batch[$idx]);
                $idx++;
            }
        }

        if ($finalize && count($this->batch)) {
            $this->batch    = array_values($this->batch);
            $newItems       = $function([], true);
            $idx            = 0;
            foreach ($newItems as $newItem) {
                if ($newItem instanceof DataInterface) {
                    yield $newItem;
                    continue;
                }
                if (!isset($this->batch[$idx])) {
                    throw new WhaskellException('Merge function items mismatch. Too many items were generated.');
                }
                yield $this->merge($this->batch[$idx], $newItem);
                unset($this->batch[$idx]);
                $idx++;
            }
            if (count($this->batch)) {
                throw new WhaskellException('Merge function items mismatch. Too few items were generated.');
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
