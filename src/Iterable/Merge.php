<?php

namespace Webbhuset\Whaskell\Iterable;

use Webbhuset\Whaskell\Constructor as F;
use Webbhuset\Whaskell\FunctionInterface;
use Webbhuset\Whaskell\WhaskellException;

class Merge implements FunctionInterface
{
    protected $function;
    protected $replace;
    protected $recursive;
    protected $batch = [];


    public function __construct($function, $replace = false, $recursive = true)
    {
        if (is_array($function)) {
            $function = F::Compose($function);
        }

        if (!$function instanceof FunctionInterface) {
            throw new WhaskellException('Function must implement FunctionInterface');
        }

        $this->function     = $function;
        $this->replace      = $replace;
        $this->recursive    = $replace;
    }

    public function __invoke($items, $finalize = true)
    {
        $function = $this->function;

        foreach ($items as $item) {
            $this->batch    = array_merge($this->batch, [$item]);
            $newItems       = $function([$item], false);
            $idx            = 0;
            foreach ($newItems as $newItem) {
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
                    if (is_array($value) && $this->recursive) {
                        $a[$key] = $this->merge($a[$key], $value);
                    } elseif ($this->replace) {
                        $a[$key] = $value;
                    }
                } else {
                    $a[$key] = $value;
                }
            }
        }

        return $a;
    }
}
