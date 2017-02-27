<?php

namespace Webbhuset\Bifrost\Core\Component\File;

use Webbhuset\Bifrost\Core\BifrostException;
use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Webbhuset\Bifrost\Core\Helper\ReflectionHelper;

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
        foreach ($items as $key => $item) {
            if (is_string($key)) {
                yield $key => $item;
                continue;
            }

            $newPath = call_user_func($this->callback, $item);

            $dir = dirname($newPath);

            if (!file_exists($dir)) {
                if (!mkdir($dir, 0777, true)) {
                    throw new BifrostException("Could not create directory '{$dir}'.");
                }
            }


            if ($this->copy) {
                if (!copy($item, $newPath)) {
                    throw new BifrostException("Could not copy '{$item}' to '{$newPath}'.");
                }
            } else {
                if (!rename($item, $newPath)) {
                    throw new BifrostException("Could not move '{$item}' to '{$newPath}'.");
                }
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
