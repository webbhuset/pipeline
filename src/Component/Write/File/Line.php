<?php

namespace Webbhuset\Bifrost\Core\Component\Write\File;

use Webbhuset\Bifrost\Core\BifrostException;
use Webbhuset\Bifrost\Core;
use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Webbhuset\Bifrost\Core\Data;

class Line implements ComponentInterface
{
    protected $file;
    protected $filename;

    public function __construct($target)
    {
        $dir = dirname($target);

        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777, true)) {
                throw new BifrostException("Could not create directory '{$dir}'.");
            }
        }

        $this->filename = $target;
        $this->file = fopen($target, 'w');

        if (!$this->file) {
            throw new BifrostException("Could not open file '{$target}' for writing.");
        }
    }

    public function process($items)
    {
        foreach ($items as $key => $item) {
            if (is_string($key)) {
                yield $key => $item;
                continue;
            }

            $bytes = fwrite($this->file, "{$item}\n");

            if ($bytes === false) {
                $item = new Data\Error("Could not write to '{$this->filename}'.", $item);
                yield 'event' => new Data\Reference($item, 'error');
            }

            yield $item;
        }
    }
}
