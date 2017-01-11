<?php
namespace Webbhuset\Bifrost\Core\Utils\Reader\File;
use \Webbhuset\Bifrost\Core\Utils\Reader\AbstractReader;
use \Webbhuset\Bifrost\Core\BifrostException;
use Webbhuset\Bifrost\Core\Utils\Logger\LoggerInterface;

class Xml extends AbstractReader
{
    protected $nodeName;
    protected $fileName;

    public function __construct(LoggerInterface $logger, $nextSteps, $params)
    {
        parent::__construct($logger, $nextSteps, $params);
        if (!isset($params['node_name'])) {
            throw new BifrostException("Parameter 'node_name' not set");
        }

        $this->nodeName = $params['node_name'];
    }

    public function init($args)
    {
        if (!is_file($args['filename'])) {
            throw new BifrostException("File not found {$args['filename']}");
        }
        $this->fileName =  $args['filename'];
        $this->xml      = new \XMLReader;
        $this->xml->open($this->fileName);
        while ($this->xml->read() && $this->xml->name !== $this->nodeName);

        parent::init($args);
    }

    public function getEntityCount()
    {
        $count = parent::getEntityCount();

        $this->xml->close();
        $this->xml->open($this->fileName);
        while ($this->xml->read() && $this->xml->name !== $this->nodeName);

        return $count;
    }

    public function finalize()
    {
        parent::finalize();
        $this->xml->close();
    }

    protected function getData()
    {
        $node      = $this->xml->readOuterXML();
        if ('' == $node) {
            return false;
        }

        $xmlObject = simplexml_load_string($node);
        $data      = $this->xmlToarray($xmlObject);

        $this->xml->next($this->nodeName);

        return [$data];
    }

    protected function xmlToarray($xml)
    {
        $result = array();

        foreach ($xml as $element) {
            $key = $element->getName();
            $e   = get_object_vars($element);
            if (!empty($e)) {
                $result[$key] = $element instanceof \SimpleXMLElement ? $this->xmlToarray($element) : $e;
            } else {
                $result[$key] = trim($element);
            }
        }

        return $result;
    }

}
