<?php

namespace Webbhuset\Whaskell\IO\File\Write;

use Webbhuset\Whaskell\AbstractFunction;
use Webbhuset\Whaskell\WhaskellException;

class Xml implements FunctionInterface
{
    protected $filename;
    protected $xml;
    protected $indentXml            = false;
    protected $indentString         = '    ';
    protected $root                 = 'Root';
    protected $attributesKey        = '@attributes';
    protected $includeDeclaration   = true;

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
            $this->indentXml = $params['indentXml'];
        }

        if (isset($params['indentString'])) {
            $this->indentString = $params['indentString'];
        }

        if (isset($params['root'])) {
            $this->root = $params['root'];
        }

        if (!$this->isNameValid($this->root)) {
            throw new WhaskellException("The XML root tag name {$this->root} is not valid.");
        }

        if (isset($params['includeDeclaration'])) {
            $this->includeDeclaration = $params['includeDeclaration'];
        }

        $this->filename = $target;

        $xml = new \XMLWriter;
        $file = $xml->openUri($target);

        if (!$file) {
            throw new WhaskellException("Could not open file {$target} for writing.");
        }

        if ($this->includeDeclaration) {
            $xml->startDocument('1.0', 'UTF-8');
        }
        $xml->setIndent($this->indentXml);
        $xml->setIndentString($this->indentString);
        $xml->startElement($this->root);

        $this->xml = $xml;
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
        foreach ($items as $item) {
            foreach ($item as $name => $data) {
                if ($name == $this->attributesKey) {
                    continue;
                }

                $this->arrayToXml($name, $data);
            }
        }

        if ($finalize) {
            $this->xml->endElement();
            $this->xml->endDocument();

            yield $this->filename;
        }
    }

    /**
     * Validates XML tag name.
     *
     * @param string $name
     * @access protected
     * @return bool
     */
    protected function isNameValid($name)
    {
        if (!preg_match('/^(([\p{L}\._\-\d]+)\:)?[\p{L}\._\-\d]+$/u', $name, $matches)) {
            // XML tag name can only contain letters, numbers, punctuation and optionally a namespace.
            return false;
        }

        if (!preg_match('/^\p{L}.*\p{L}$/u', $name)) {
            // XML tag cannot start with non word chars.
            return false;
        }

        if (preg_match('/^xml/i', $name)) {
            // XML tag cannot start with the letters 'xml'.
            return false;
        }

        return true;
    }

    /**
     * Validates XML attribute name.
     *
     * @param string $name
     * @access protected
     * @return bool
     */
    protected function isAttributeKeyValid($key)
    {
        if (!preg_match('/^\p{L}+$/u', $key)) {
            // XML tag name can only contain letters, numbers and punctuation
            return false;
        }

        return true;
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
            if (!is_array($data)) {
                $xml->text($data);
                return;
            }
            foreach ($data as $key => $value) {
                $this->arrayToXml($key, $value);
            }

            return;
        }

        if (!$this->isNameValid($name)) {
            return;
        }

        $xml->startElement($name);

        if ($this->hasChildren($data)) {
            $this->writeAttributes($xml, $data);
            unset($data[$this->attributesKey]);

            $firstChild = true;
            foreach ($data as $key => $value) {
                if (!$firstChild && is_int($key)) {
                    $xml->endElement();
                    $xml->startElement($name);
                }
                $firstChild = false;

                $this->arrayToXml($key, $value);
            }
        } else {
            $xml->text($data);
        }

        $xml->endElement();
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
            if (!$this->isAttributeKeyValid($name)) {
                continue;
            }
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
