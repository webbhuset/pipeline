<?php

namespace Webbhuset\Whaskell\IO\File\Read;

use Webbhuset\Whaskell\AbstractFunction;
use Webbhuset\Whaskell\WhaskellException;

class Raw extends AbstractFunction
{
    protected function invoke($files, $finalize = true)
    {
        foreach ($files as $key => $filename) {
            if (!is_file($filename)) {
                $msg = "File not found '{$filename}'.";
                if ($this->observer) {
                    $this->observer->observeError($filename, $error);
                }

                continue;
            }

            yield file_get_contents($filename);
        }
    }
}
