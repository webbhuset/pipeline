<?php

namespace Webbhuset\Whaskell\IO\File\Read;

use Webbhuset\Whaskell\AbstractFunction;
use Webbhuset\Whaskell\WhaskellException;

class Line implements FunctionInterface
{
    protected $ignoreEmpty = true;

    public function __construct(array $config = [])
    {
        if (isset($config['ignore_empty'])) {
            $this->ignoreEmpty = $config['ignore_empty'];
        }
    }

    public function __invoke($files, $finalize = true)
    {
        foreach ($files as $key => $filename) {
            if (!is_file($filename)) {
                $msg = "File not found '{$filename}'.";
                if ($this->observer) {
                    $this->observer->observeError($filename, $error);
                }

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
