<?php

namespace Webbhuset\Bifrost\Core\Component\Read\File;

use Webbhuset\Bifrost\Core\BifrostException;
use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use XMLReader;

class Xml implements ComponentInterface
{
    protected $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function process($files)
    {
        foreach ($files as $key => $filename) {
            if (is_string($key)) {
                yield $key => $filename;
                continue;
            }
            if (!is_file($filename)) {
                $msg = "File not found {$filename}";
                $item = new Data\Error($msg, $filename);
                yield 'event' => new Data\Reference($item, 'error');
                continue;
            }

            $reader = new XMLReader;
            $reader->open($filename);

            $name = $this->findStart($reader);

            do {
                $node = trim($reader->readOuterXML());
                if (!$node) {
                    continue;
                }
                $xmlObject = simplexml_load_string($node);
                $item = [
                    $xmlObject->getName() => json_decode(json_encode($xmlObject), true),
                ];
                yield $item;
            } while ($name ? $reader->next($name) : $reader->next());
        }
    }

    protected function findStart($reader)
    {
        $path   = explode('/', $this->path);
        $level  = 0;

        $reader->read();

        while (true) {
            if (substr($reader->name, 0, 1) == '#') {
                $reader->read();
                continue;
            }
            $name = $path[$level];

            if ($reader->name == $name || $name == '*') {
                if ($level == count($path) - 1) {
                    break;
                }
                $level += 1;
                $reader->read();
            } else {
                $reader->next();
            }
        }

        if ($name == '*') {
            $name = null;
        }

        return $name;
    }
}
