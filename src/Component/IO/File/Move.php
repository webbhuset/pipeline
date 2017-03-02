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
                $msg    = 'Item is not a string.';
                yield new ErrorData($item, $msg);
                continue;
            }

            $oldPath = $item;

            if (!file_exists($oldPath)) {
                $msg    = "'{$oldPath}' does not exist.";
                yield new ErrorData($item, $msg);
                continue;
            }

            $newPath    = call_user_func($this->callback, $oldPath);
            $dir        = dirname($newPath);

            if (!file_exists($dir)) {
                if (!mkdir($dir, 0777, true)) {
                    $msg    = "Could not create directory '{$dir}'.";
                    yield new ErrorData($item, $msg);
                    continue;
                }
            }


            if ($this->copy) {
                if (!copy($oldPath, $newPath)) {
                    $msg    = "Could not copy '{$oldPath}' to '{$newPath}'.";
                    yield new ErrorData($item, $msg);
                    continue;
                }
                $eventName = 'copy_file';
            } else {
                if (!rename($oldPath, $newPath)) {
                    $msg    = "Could not move '{$oldPath}' to '{$newPath}'.";
                    yield new ErrorData($item, $msg);
                    continue;
                }
                $eventName = 'move_file';
            }

            $data = ['from' => $oldPath, 'to' => $newPath];
            yield new EventData($eventName, $item, $data);

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
