<?php

namespace Webbhuset\Bifrost\Core\Component\IO\File\Read;

use Webbhuset\Bifrost\Core\BifrostException;
use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Webbhuset\Bifrost\Core\Data\ActionData\ActionDataInterface;
use Webbhuset\Bifrost\Core\Data\ActionData\ErrorData;

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
