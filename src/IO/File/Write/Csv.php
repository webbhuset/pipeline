<?php

namespace Webbhuset\Whaskell\IO\File\Write;

use Webbhuset\Whaskell\AbstractFunction;
use Webbhuset\Whaskell\WhaskellException;

class Csv extends AbstractFunction
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

        $writeMode = 'w';
        if (isset($params['append']) && $params['append'] == true) {
            $writeMode            = 'a';
            $this->headersWritten = true;
        }

        $dir = dirname($target);

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $this->file = fopen($target, $writeMode);

        if (!$this->file) {
            throw new WhaskellException("Could not open file {$target} for writing.");
        }
        $this->filename = $target;
    }

    protected function invoke($items, $finalize = true)
    {
        foreach ($items as $item) {
            if (!$this->headersWritten) {
                $this->putRow(array_keys($item));
                $this->headersWritten = true;
            }

            $bytes = $this->putRow($item);

            if ($bytes == false) {
                $msg = "Could not write to '{$this->filename}'.";
                if ($this->observer) {
                    $this->observe->observeEvent($item, $msg);
                }
            }
        }

        if ($finalize) {
            fclose($this->file);
            yield $this->filename;
        }
    }

    protected function putRow($data)
    {
        return fputcsv($this->file, $data, $this->separator, $this->enclosure);
    }
}
