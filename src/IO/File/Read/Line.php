<?php

namespace Webbhuset\Whaskell\IO\File\Read;

use Webbhuset\Whaskell\WhaskellException;
use Webbhuset\Whaskell\Dispatch\Data\DataInterface;
use Webbhuset\Whaskell\Dispatch\Data\ErrorData;

class Line
{
    protected $ignoreEmpty = true;

    public function __construct(array $config = [])
    {
        if (isset($config['ignore_empty'])) {
            $this->ignoreEmpty = $config['ignore_empty'];
        }
    }

    public function __invoke($files)
    {
        foreach ($files as $key => $filename) {
            if ($filename instanceof DataInterface) {
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
