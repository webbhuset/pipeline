<?php

namespace Webbhuset\Bifrost\Component\IO\File\Read;

use Webbhuset\Bifrost\BifrostException;
use Webbhuset\Bifrost\Component\ComponentInterface;
use Webbhuset\Bifrost\Data\ActionData\ActionDataInterface;
use Webbhuset\Bifrost\Data\ActionData\ErrorData;

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
            if ($filename instanceof ActionDataInterface) {
                yield $filename;
                continue;
            }

            if (!is_file($filename)) {
                $msg = "File not found '{$filename}'.";
                yield new ErrorData($filename, $msg);
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
