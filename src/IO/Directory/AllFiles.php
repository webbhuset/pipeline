<?php

namespace Webbhuset\Whaskell\IO\Directory;

use DirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Webbhuset\Whaskell\FunctionInterface;
use Webbhuset\Whaskell\WhaskellException;

class AllFiles implements FunctionInterface
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

    public function __invoke($items, $finalize = true)
    {
        foreach ($items as $item) {
            if (!is_dir($item)) {
                $msg = "Directory '{$item}' does not exist.";
                if ($this->observer) {
                    $this->observer->observeError($item, $msg);
                }

                continue;
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
