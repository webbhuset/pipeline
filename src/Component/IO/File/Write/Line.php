<?php

namespace Webbhuset\Bifrost\Core\Component\IO\File\Write;

use Webbhuset\Bifrost\Core\BifrostException;
use Webbhuset\Bifrost\Core;
use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Webbhuset\Bifrost\Core\Data\ActionData\ActionDataInterface;
use Webbhuset\Bifrost\Core\Data\ActionData\ErrorData;

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
        foreach ($items as $item) {
            if ($item instanceof ActionDataInterface) {
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
