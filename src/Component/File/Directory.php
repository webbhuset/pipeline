<?php

namespace Webbhuset\Bifrost\Core\Component\File;

use Webbhuset\Bifrost\Core\BifrostException;
use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use DirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class Directory implements ComponentInterface
{
    protected $recursive        = false;
    protected $onlyFiles        = true;
    protected $pathname         = true;
    protected $relative         = false;

    public function __construct($config = [])
    {
        if (isset($config['recursive'])) {
            $this->recursive = $config['recursive'];
        }
        if (isset($config['onlyFiles'])) {
            $this->onlyFiles = $config['onlyFiles'];
        }
        if (isset($config['pathname'])) {
            $this->pathname = $config['pathname'];
        }
        if (isset($config['relative'])) {
            $this->relative = $config['relative'];
        }
    }

    public function process($items)
    {
        foreach ($items as $key => $item) {
            if (is_string($key)) {
                yield $key => $item;
                continue;
            }

            if (!is_dir($item)) {
                throw new BifrostException("Dir {$item} does not exists.");
            }
            $iterator = $this->getIterator($item);

            foreach ($iterator as $file) {
                if ($this->onlyFiles && !$file->isFile()) {
                    continue;
                }
                if ($this->pathname) {
                    yield $this->getPath($file, $item);
                } else {
                    yield $file;
                }
            }
        }
    }

    protected function getIterator($dirname)
    {
        if ($this->recursive) {
            $directory = new RecursiveDirectoryIterator($dirname);
            $iterator  = new RecursiveIteratorIterator($directory);
        } else {
            $iterator = new DirectoryIterator($dirname);
        }

        return $iterator;
    }

    protected function getPath($file, $dirname)
    {
        return $this->relative
               ? substr($file->getPathname(), strlen($dirname) + 1)
               : $file->getPathname();
    }
}
