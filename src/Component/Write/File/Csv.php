<?php

namespace Webbhuset\Bifrost\Core\Component\Write\File;

use Webbhuset\Bifrost\Core\BifrostException;
use Webbhuset\Bifrost\Core;
use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Webbhuset\Bifrost\Core\Data;

class Csv implements ComponentInterface
{
    protected $separator    = ',';
    protected $enclosure    = '"';
    protected $columnCount  = false;
    protected $file;
    protected $filename;
    protected $headersWritten = false;

    public function __construct($target, array $params = [])
    {
        if (isset($params['separator'])) {
            $this->separator = $params['separator'];
        }
        if (isset($params['enclosure'])) {
            $this->enclosure = $params['enclosure'];
        }

        $dir = dirname($target);

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $this->file = fopen($target, 'w');

        if (!$this->file) {
            throw new BifrostException("Could not open file {$target} for writing.");
        }
        $this->filename = $target;
    }

    public function process($items)
    {
        foreach ($items as $key => $item) {
            if (is_string($key)) {
                yield $key => $item;
                continue;
            }
            if (!$this->headersWritten) {
                $this->putRow(array_keys($item));
                $this->headersWritten = true;
            }

            $bytes = $this->putRow($item);

            if ($bytes == false) {
                $item = new Data\Error("Could not write to '{$this->filename}'", $item);
                yield 'event' => new Data\Reference($item, 'error');
            } else {
                yield $bytes;
            }
        }
    }

    protected function putRow($data)
    {
        return fputcsv($this->file, $data, $this->separator, $this->enclosure);
    }
}
