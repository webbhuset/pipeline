<?php

namespace Webbhuset\Whaskell\IO\File;

use Webbhuset\Whaskell\AbstractFunction;
use Webbhuset\Whaskell\FunctionSignature;
use Webbhuset\Whaskell\WhaskellException;

class Move extends AbstractFunction
{
    protected $oldPathCallback;
    protected $newPathCallback;
    protected $copy = false;

    public function __construct($oldPathCallback, $newPathCallback = null, $config = [])
    {
        if (!is_callable($newPathCallback)) {
            $newPathCallback    = $oldPathCallback;
            $oldPathCallback    = function($item) {
                return $item;
            };
            $config             = $newPathCallback ?: [];
        }

        $canBeUsed = FunctionSignature::canBeUsedWithArgCount($oldPathCallback, 1);
        if ($canBeUsed !== true) {
            throw new WhaskellException('$oldPathCallback: ' . $canBeUsed . ' Eg. function($item)');
        }

        $canBeUsed = FunctionSignature::canBeUsedWithArgCount($newPathCallback, 1);
        if ($canBeUsed !== true) {
            throw new WhaskellException('$newPathCallback: ' . $canBeUsed . ' Eg. function($item)');
        }

        $this->oldPathCallback = $oldPathCallback;
        $this->newPathCallback = $newPathCallback;

        if (array_key_exists('copy', $config)) {
            $this->copy = $config['copy'];
        }
    }

    protected function invoke($items, $finalize = true)
    {
        foreach ($items as $item) {
            $oldPath = call_user_func($this->oldPathCallback, $item);

            if (!is_string($oldPath)) {
                $msg = 'File to move is not a string.';
                $this->error($item, $msg);

                continue;
            }

            if (!file_exists($oldPath)) {
                $msg = "File '{$oldPath}' does not exist.";
                $this->error($item, $msg);

                continue;
            }

            $newPath    = call_user_func($this->newPathCallback, $item);
            $dir        = dirname($newPath);

            if (!is_dir($dir)) {
                if (is_file($dir)) {
                    $msg = "Could not create directory '{$dir}', file exists with same path.";
                    $this->error($item, $msg);

                    continue;
                } elseif (!mkdir($dir, 0777, true)) {
                    $msg = "Could not create directory '{$dir}'.";
                    $this->error($item, $msg);

                    continue;
                }
            }

            if (!copy($oldPath, $newPath)) {
                $msg = "Could not copy '{$oldPath}' to '{$newPath}'.";
                $this->error($item, $msg);

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
