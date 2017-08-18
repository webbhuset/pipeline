<?php

namespace Webbhuset\Whaskell\IO\File;

use Webbhuset\Whaskell\WhaskellException;
use Webbhuset\Whaskell\Dispatch\Data\DataInterface;
use Webbhuset\Whaskell\Dispatch\Data\EventData;
use Webbhuset\Whaskell\Dispatch\Data\ErrorData;
use Webbhuset\Whaskell\FunctionSignature;

class Move
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

    public function __invoke($items)
    {
        foreach ($items as $item) {
            if ($item instanceof DataInterface) {
                yield $item;
                continue;
            }

            if (!is_string($item)) {
                $msg = 'Item is not a string.';
                yield new ErrorData($item, $msg);
                yield false;
                continue;
            }

            $oldPath = $item;

            if (!file_exists($oldPath)) {
                $msg = "File '{$oldPath}' does not exist.";
                yield new ErrorData($item, $msg);
                yield false;
                continue;
            }

            $newPath    = call_user_func($this->callback, $oldPath);
            $dir        = dirname($newPath);

            if (!is_dir($dir)) {
                if (is_file($dir)) {
                    $msg = "Could not create directory '{$dir}', file exists with same path.";
                    yield new ErrorData($item, $msg);
                    yield false;
                    continue;
                } elseif (!mkdir($dir, 0777, true)) {
                    $msg = "Could not create directory '{$dir}'.";
                    yield new ErrorData($item, $msg);
                    yield false;
                    continue;
                }
            }

            if (!copy($oldPath, $newPath)) {
                $msg = "Could not copy '{$oldPath}' to '{$newPath}'.";
                yield new ErrorData($item, $msg);
                yield false;
                continue;
            }


            $type = $this->copy ? 'Copied' : 'Moved';
            $msg = "{$type} '{$oldPath}' to '{$newPath}'.";
            yield new EventData('info', $msg);

            if (!$this->copy && !unlink($oldPath)) {
                $msg = "Could not unlink '{$oldPath}' after copying.";
                yield new ErrorData($item, $msg);
            }

            yield $newPath;
        }
    }
}
