<?php

namespace Webbhuset\Whaskell\IO\File;

use Webbhuset\Whaskell\AbstractFunction;
use Webbhuset\Whaskell\FunctionSignature;
use Webbhuset\Whaskell\WhaskellException;

class Move extends AbstractFunction
{
    protected $callback;
    protected $copy = false;

    public function __construct($callback, $config = [])
    {
        $canBeUsed = FunctionSignature::canBeUsedWithArgCount($callback, 1);

        if ($canBeUsed !== true) {
            throw new WhaskellException($canBeUsed . ' Eg. function($item)');
        }

        $this->callback = $callback;

        if (isset($config['copy'])) {
            $this->copy = $config['copy'];
        }
    }

    protected function invoke($items, $finalize = true)
    {
        foreach ($items as $item) {
            if (!is_string($item)) {
                $msg = 'Item is not a string.';
                $this->error($item, $msg);

                yield $item;
                continue;
            }

            $oldPath = $item;

            if (!file_exists($oldPath)) {
                $msg = "File '{$oldPath}' does not exist.";
                $this->error($item, $msg);

                yield $oldPath;
                continue;
            }

            $newPath    = call_user_func($this->callback, $oldPath);
            $dir        = dirname($newPath);

            if (!is_dir($dir)) {
                if (is_file($dir)) {
                    $msg = "Could not create directory '{$dir}', file exists with same path.";
                    $this->error($item, $msg);

                    yield $oldPath;
                    continue;
                } elseif (!mkdir($dir, 0777, true)) {
                    $msg = "Could not create directory '{$dir}'.";
                    $this->error($item, $msg);

                    yield $oldPath;
                    continue;
                }
            }

            if (!copy($oldPath, $newPath)) {
                $msg = "Could not copy '{$oldPath}' to '{$newPath}'.";
                $this->error($item, $msg);

                yield $oldPath;
                continue;
            }


            if ($this->observer) {
                $type   = $this->copy ? 'Copied' : 'Moved';
                $msg    = "{$type} '{$oldPath}' to '{$newPath}'.";
                $this->observer->observeEvent('log', $msg);
            }

            if (!$this->copy && !unlink($oldPath)) {
                $msg = "Could not unlink '{$oldPath}' after copying.";
                $this->error($item, $msg);
            }

            yield $newPath;
        }
    }

    protected function error($item, $msg)
    {
        if ($this->observer) {
            $this->observer->observeError($item, $msg);
        }
    }
}
