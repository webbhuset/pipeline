<?php

namespace Webbhuset\Whaskell\IO\File\Write;

use Webbhuset\Whaskell\AbstractFunction;
use Webbhuset\Whaskell\WhaskellException;

class Line extends AbstractFunction
{
    protected $file;
    protected $filename;

    public function __construct($target)
    {
        $dir = dirname($target);

        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777, true)) {
                throw new WhaskellException("Could not create directory '{$dir}'.");
            }
        }

        $this->filename = $target;
        $this->file = fopen($target, 'w');

        if (!$this->file) {
            throw new WhaskellException("Could not open file '{$target}' for writing.");
        }
    }

    protected function invoke($items, $finalize = true)
    {
        foreach ($items as $item) {
            $bytes = fwrite($this->file, "{$item}\n");

            if ($bytes === false) {
                $msg = "Could not write to '{$this->filename}'.";
                if ($this->observer) {
                    $this->observer->observeError($item, $msg);
                }
            }
        }

        if ($finalize) {
            yield $this->filename;
        }
    }
}
