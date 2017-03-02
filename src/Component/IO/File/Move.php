<?php

namespace Webbhuset\Bifrost\Core\Component\IO\File;

use Webbhuset\Bifrost\Core\BifrostException;
use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Webbhuset\Bifrost\Core\Helper\ReflectionHelper;
use Webbhuset\Bifrost\Core\Data\ActionData\ActionDataInterface;
use Webbhuset\Bifrost\Core\Data\ActionData\EventData;
use Webbhuset\Bifrost\Core\Data\ActionData\ErrorData;

class Move implements ComponentInterface
{
    protected $callback;
    protected $copy         = false;

    public function __construct($callback, $config = [])
    {
        if (!is_callable($callback)) {
            throw new BifrostException('Callback parameter is not callable.');
        }

        $this->validateCallback($callback);

        $this->callback = $callback;

        if (isset($config['copy'])) {
            $this->copy = $config['copy'];
        }
    }

    public function process($items)
    {
        foreach ($items as $item) {
            if ($item instanceof ActionDataInterface) {
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
                $msg = "'{$oldPath}' does not exist.";
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

            $eventName = $this->copy ? 'fileCopied' : 'fileMoved';
            $data = ['from' => $oldPath, 'to' => $newPath];
            yield new EventData($eventName, $item, $data);

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
            throw new BifrostException('Could not create reflection from callback parameter.');
        }

        $params = $reflection->getParameters();

        if (count($params) < 1) {
            throw new BifrostException('The callback requires 1 param. function($item)');
        }
        if (count($params) > 1) {
            foreach ($params as $idx => $param) {
                if ($idx == 0) {
                    continue;
                }
                if (!$param->isOptional()) {
                    $idx += 1;
                    throw new BifrostException("Callback function param {$idx} is not optional. All params except first has to be optional.");
                }
            }
        }
    }
}
