<?php

namespace Webbhuset\Whaskell\IO\File\Write;

use Webbhuset\Whaskell\WhaskellException;
use Webbhuset\Whaskell\Dispatch\Data\DataInterface;
use Webbhuset\Whaskell\Dispatch\Data\ErrorData;

class Line
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

    public function __invoke($items)
    {
        foreach ($items as $item) {
            if ($item instanceof DataInterface) {
                yield $item;
                continue;
            }

            $bytes = fwrite($this->file, "{$item}\n");

            if ($bytes === false) {
                $msg = "Could not write to '{$this->filename}'.";
                yield new ErrorData($item, $msg);
            }

            yield $item;
        }
    }
}
