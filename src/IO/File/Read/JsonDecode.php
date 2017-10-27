<?php

namespace Webbhuset\Whaskell\IO\File\Read;

use Webbhuset\Whaskell\AbstractFunction;
use Webbhuset\Whaskell\WhaskellException;

class JsonDecode extends AbstractFunction
{
    /**
     * Depth of items in json file.
     *
     * @var int
     */
    protected $itemDepth = 1;


    public function __construct(array $params = [])
    {
        if (isset($params['depth'])) {
            $this->itemDepth = $params['depth'];
        }
    }

    protected function invoke($files, $finalize = true)
    {
        foreach ($files as $filename) {
            if (!is_file($filename)) {
                $msg = "File not found {$filename}";
                if ($this->observer) {
                    $this->observer->observeError($filename, $msg);
                }

                continue;
            }

            $jsonData = json_decode(file_get_contents($filename), true);

            $items = $this->getItems($jsonData);

            foreach ($items as $item) {
                yield $item;
            }
        }
    }

    protected function getItems($data, $depth = 0)
    {
        if ($depth == $this->itemDepth) {
            yield $data;
        }

        if (!is_array($data)) {
            return;
        }

        foreach ($data as $array) {
            $items = $this->getItems($array, $depth + 1);
            foreach ($items as $item) {
                yield $item;
            }
        }
    }
}
