<?php
namespace Webbhuset\Bifrost\Core\Utils\Reader\File;
use \Webbhuset\Bifrost\Core\Utils\Reader\AbstractReader;
use \Webbhuset\Bifrost\Core\BifrostException;

class Csv extends AbstractReader
{
    /**
     * Current row.
     *
     * @var integer
     */
    protected $current = 0;

    /**
     * Csv headers.
     *
     * @var array
     */
    protected $headers;

    /**
     * Csv separator.
     *
     * @var string
     */
    protected $separator = ';';

    /**
     * Csv enclosure.
     *
     * @var string
     */
    protected $enclosure = '"';

    /**
     * If headers should be trimmed.
     *
     * @var boolean
     */
    protected $trimHeaders = true;

    /**
     * If rows should be trimmed.
     *
     * @var boolean
     */
    protected $trimRows = true;

    public function init($args)
    {
        if (!is_file($args['filename'])) {
            throw new BifrostException("File not found {$args['filename']}");
        }
        $this->file = fopen($args['filename'], 'r');
        $row        = $this->getNextRow();

        foreach ($row as $value) {
            $this->addHeader($value);
        }

        parent::init($args);
    }

    public function getEntityCount()
    {
        $count = parent::getEntityCount();

        fseek($this->file, 0, SEEK_SET);
        $this->current = 0;
        $this->getNextRow();

        return $count;
    }

    public function finalize()
    {
        parent::finalize();
        fclose($this->file);
    }

    protected function getData()
    {
        $done = false;
        while (!$done) {
            $done       = true;
            $row        = $this->getNextRow();
            $headers    = $this->headers;

            if (!$row) {
                return false;
            }
            $this->current++;

            if ($this->trimRows) {
                foreach ($row as $key => $value) {
                    $row[$key] = trim($value);
                }
            }

            if (count($row) != count($headers)) {
                if (count($row) === 1) {
                    $this->logger->log("Empty row on entity number {$this->current}");
                } else {
                    $this->logger->log("Error on entity number {$this->current}, header-row mismatch.");
                }
                $done = false;
            }
        }

        return [array_combine($headers, $row)];
    }

    /**
     * Returns next csv item.
     *
     * @return array
     */
    protected function getNextRow()
    {
        return fgetcsv($this->file, 0, $this->separator, $this->enclosure);
    }

    /**
     * Adds header to header array.
     *
     * @param string $value
     * @param int $num
     *
     * @return void
     */
    protected function addHeader($value, $num = 0)
    {
        if ($this->trimHeaders) {
            $value = trim($value);
        }

        $key    = $num
                ? "{$value}_{$num}"
                : $value;

        if (isset($this->headers[$key])) {
            return $this->addHeader($value, $num + 1);
        }

        $this->headers[$key] = $key;
    }
}
