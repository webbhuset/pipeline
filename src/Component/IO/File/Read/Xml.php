<?php

namespace Webbhuset\Bifrost\Component\IO\File\Read;

use Webbhuset\Bifrost\BifrostException;
use Webbhuset\Bifrost\Component\ComponentInterface;
use Webbhuset\Bifrost\Data\ActionData\ActionDataInterface;
use Webbhuset\Bifrost\Data\ActionData\ErrorData;
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
            if ($filename instanceof ActionDataInterface) {
                yield $filename;
                continue;
            }
            if (!is_file($filename)) {
                $msg = "File not found '{$filename}'.";
                yield new ErrorData($filename, $msg);
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
                $xmlObject = simplexml_load_string($node, null, LIBXML_NOCDATA);
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
