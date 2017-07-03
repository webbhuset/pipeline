<?php

namespace Webbhuset\Whaskell\IO\File;

use Webbhuset\Whaskell\WhaskellException;
use Webbhuset\Whaskell\ReflectionHelper;
use Webbhuset\Whaskell\Dispatch\Data\DataInterface;
use Webbhuset\Whaskell\Dispatch\Data\EventData;
use Webbhuset\Whaskell\Dispatch\Data\ErrorData;

class Move
{
    protected $callback;
    protected $copy         = false;

    public function __construct($callback, $config = [])
    {
        if (!is_callable($callback)) {
            throw new WhaskellException('Callback parameter is not callable.');
        }

        $this->validateCallback($callback);

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

    protected function validateCallback($callback)
    {
        $reflection = ReflectionHelper::getReflectionFromCallback($callback);

        if (!$reflection) {
            throw new WhaskellException('Could not create reflection from callback parameter.');
        }

        $params = $reflection->getParameters();

        if (count($params) < 1) {
            throw new WhaskellException('The callback requires 1 param. function($item)');
        }
        if (count($params) > 1) {
            foreach ($params as $idx => $param) {
                if ($idx == 0) {
                    continue;
                }
                if (!$param->isOptional()) {
                    $idx += 1;
                    throw new WhaskellException("Callback function param {$idx} is not optional. All params except first has to be optional.");
                }
            }
        }
    }
}
