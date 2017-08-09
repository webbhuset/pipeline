<?php

namespace Webbhuset\Whaskell\IO\File\Write;

use Webbhuset\Whaskell\WhaskellException;
use Webbhuset\Whaskell\Dispatch\Data\DataInterface;
use Webbhuset\Whaskell\Dispatch\Data\ErrorData;

class Xml
{
    protected $file;
    protected $filename;
    protected $xml;
    protected $intentXml = false;

    protected $attributesKey    = '@attributes';

    /**
     * Xml constructor.
     *
     * @param $target
     * @param array $params
     *
     * @throws WhaskellException
     */
    public function __construct($target, array $params = [])
    {
        $dir = dirname($target);

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        if (isset($params['indentXml'])) {
            $this->intentXml = $params['indentXml'];
        }

        $this->file = new \XmlWriter;
        $file = $this->file->openUri($target);
        if (!$file) {
            throw new WhaskellException("Could not open file {$target} for writing.");
        }

        $this->filename = $target;

        $this->xml = new \XMLWriter;
        $this->xml->openMemory();
        $this->xml->startDocument('1.0', 'UTF-8');
        $this->xml->setIndent($this->intentXml);
    }

    /**
     * Invoke
     *
     * @param $items
     * @param bool $finalize
     *
     * @return \Generator
     */
    public function __invoke($items, $finalize = true)
    {
        foreach ($items as $name => $item) {
            if ($item instanceof DataInterface) {
                yield $item;
                continue;
            }

            $this->arrayToXml($name, $item);
        }

        if ($finalize) {
            $this->file->endDocument();

            yield $this->filename;
        }
    }

    /**
     * Turns an array to xml
     * Nodes should have _name and _data
     * _attributes optional
     *
     * @param $name
     * @param $data
     *
     * @return void
     */
    protected function arrayToXml($name, $data)
    {
        $xml = $this->xml;
        if (is_int($name)) {
            foreach ($data as $key => $value) {
                $this->arrayToXml($key, $value);
            }

            return;
        }

        $xml->startElement($name);

        if ($this->hasChildren($data)) {
            $this->writeAttributes($xml, $data);
            unset($data[$this->attributesKey]);

            foreach ($data as $key => $value) {
                $this->arrayToXml($key, $value);
            }
        } else {
            $xml->text($data);
        }

        $xml->endElement();

        $this->file->writeRaw($xml->outputMemory(true));
    }

    /**
     * Write attributes
     *
     * @param \XMLWriter $xml
     * @param $data
     */
    protected function writeAttributes(\XMLWriter $xml, $data)
    {
        $attributes = isset($data[$this->attributesKey]) ? $data[$this->attributesKey] : [];

        foreach ($attributes as $name => $value) {
            $xml->writeAttribute($name, $value);
        }
    }

    /**
     * Check if node has children
     *
     * @param $data
     *
     * @return bool
     */
    protected function hasChildren($data)
    {
        return is_array($data);
    }
}
