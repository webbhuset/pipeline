<?php

namespace Webbhuset\Whaskell\IO\File\Read;

use Webbhuset\Whaskell\WhaskellException;
use Webbhuset\Whaskell\Dispatch\Data\DataInterface;
use Webbhuset\Whaskell\Dispatch\Data\ErrorData;
use XMLReader;

class Xml
{
    protected $path;

    public function __construct($path)
    {
        $this->path = $path;
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
