<?php

namespace Webbhuset\Bifrost\Component\IO\File\Read;

use Webbhuset\Bifrost\BifrostException;
use Webbhuset\Bifrost\Component\ComponentInterface;
use Webbhuset\Bifrost\Data\ActionData\ActionDataInterface;
use Webbhuset\Bifrost\Data\ActionData\ErrorData;

class Csv implements ComponentInterface
{
    protected $separator    = ',';
    protected $enclosure    = '"';
    protected $trimHeaders  = true;
    protected $columnCount  = false;

    public function __construct(array $params = [])
    {
        if (isset($params['separator'])) {
            $this->separator = $params['separator'];
        }
        if (isset($params['enclosure'])) {
            $this->enclosure = $params['enclosure'];
        }
        if (isset($params['trim_headers'])) {
            $this->trimHeaders = $params['trim_headers'];
        }
        if (isset($params['column_count'])) {
            $this->columnCount = $params['column_count'];
        }
    }

    public function process($files)
    {
        foreach ($files as $filename) {
            if ($filename instanceof ActionDataInterface) {
                yield $filename;
                continue;
            }
            if (!is_file($filename)) {
                $msg = "File not found {$filename}";
                yield new ErrorData($filename, $msg);
                continue;
            }

            $file = fopen($filename, 'r');

            $headers = $this->getNextRow($file);

            if ($this->columnCount) {
                if (count($headers) != $this->columnCount) {
                    $msg = "Column count mismatch in {$filename}";
                    yield new ErrorData($filename, $msg);
                    continue;
                }
            }
            $headers = $this->makeHeadersUnique($headers);
            $rowNumber = 1;

            while (!feof($file)) {
                $row = $this->getNextRow($file);

                if (!$row) {
                    break;
                }
                $rowNumber += 1;
                if (count($row) != count($headers)) {
                    if (count($row) == 1 && $row[0] === null) {
                        $msg = "Empty row in file {$filename}:{$rowNumber}";
                    } else {
                        $msg = "Header-row column missmatch in file {$filename}:{$rowNumber}";
                    }
                    yield new ErrorData($filename, $msg);
                    continue;
                }

                $item = array_combine($headers, $row);
                yield $item;
            }

            fclose($file);
        }
    }

    protected function getNextRow($file)
    {
        return fgetcsv($file, 0, $this->separator, $this->enclosure);
    }

    protected function makeHeadersUnique($headers)
    {
        $uniqueHeaders = [];

        foreach ($headers as $header) {
            $i = 1;
            $unique = $this->trimHeaders
                    ? trim($header)
                    : $header;

            while (isset($uniqueHeaders[$unique])) {
                $unique = $header . '_' . $i;
                $i += 1;
            }
            $uniqueHeaders[$unique] = 1;
        }

        return array_keys($uniqueHeaders);
    }
}
