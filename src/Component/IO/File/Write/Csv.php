<?php

namespace Webbhuset\Bifrost\Component\IO\File\Write;

use Webbhuset\Bifrost\BifrostException;
use Webbhuset\Bifrost\Component\ComponentInterface;
use Webbhuset\Bifrost\Data\ActionData\ActionDataInterface;
use Webbhuset\Bifrost\Data\ActionData\ErrorData;

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
        foreach ($items as $item) {
            if ($item instanceof ActionDataInterface) {
                yield $item;
                continue;
            }
            if (!$this->headersWritten) {
                $this->putRow(array_keys($item));
                $this->headersWritten = true;
            }

            $bytes = $this->putRow($item);

            if ($bytes == false) {
                $msg = "Could not write to '{$this->filename}'.";
                yield new ErrorData($item, $msg);
            }

            yield $item;
        }
    }

    protected function putRow($data)
    {
        return fputcsv($this->file, $data, $this->separator, $this->enclosure);
    }
}
