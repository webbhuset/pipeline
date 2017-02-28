<?php

namespace Webbhuset\Bifrost\Core\Component\Read\File;

use Webbhuset\Bifrost\Core\BifrostException;
use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Webbhuset\Bifrost\Core\Data;

class Line implements ComponentInterface
{
    protected $ignoreEmpty = true;

    public function __construct(array $config = [])
    {
        if (isset($config['ignore_empty'])) {
            $this->ignoreEmpty = $config['ignore_empty'];
        }
    }

    public function process($files)
    {
        foreach ($files as $key => $filename) {
            if (is_string($key)) {
                yield $key => $filename;
                continue;
            }

            if (!is_file($filename)) {
                $msg = "File not found '{$filename}'.";
                $item = new Data\Error($msg, $filename);
                yield 'event' => new Data\Reference($item, 'error');
                continue;
            }

            $file = fopen($filename, 'r');

            while (!feof($file)) {
                $line = rtrim(fgets($file), PHP_EOL);

                if ($this->ignoreEmpty && !$line) {
                    continue;
                }

                yield $line;
            }

            fclose($file);
        }
    }
}
