<?php

namespace Webbhuset\Bifrost\Core\Component\Fetch;

use Webbhuset\Bifrost\Core\BifrostException;
use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use DirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class Directory implements ComponentInterface
{
    protected $recursive        = false;
    protected $onlyFiles        = true;
    protected $pathname         = false;
    protected $relative         = false;

    public function __construct($config)
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

    public function process($dirname)
    {
        if (!is_dir($dirname)) {
            throw new BifrostException("Dir {$dirname} does not exists.");
        }
        $iterator = $this->getIterator($dirname);

        foreach ($iterator as $file) {
            if ($this->onlyFiles && !$file->isFile()) {
                continue;
            }
            if ($this->pathname) {

                yield $this->getPath($file, $dirname);
            } else {
                yield $file;
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
