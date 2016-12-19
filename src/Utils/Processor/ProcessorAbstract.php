<?php
namespace Webbhuset\Bifrost\Core\Utils\Processor;
use \Webbhuset\Bifrost\Core\Utils as Utils;
abstract class ProcessorAbstract
    implements Utils\Reader\ReaderInterface, Utils\Writer\WriterInterface
{
    protected $parent;

    public function __construct($params){
        if (isset($params['parent'])) {
            if ((!$params['parent'] instanceof Utils\Reader\ReaderInterface)
                || (!$params['parent'] instanceof Utils\Reader\WriterInterface))
            {
                throw new \Webbhuset\Bifrost\Core\BifrostException("Parent must implement reader or witer interface");
            }
            $this->parent = $params['parent'];
        }

    }

    public function init($args)
    {
        return $this->parent->init($args);
    }

    public function getEntityCount()
    {
        return $this->parent->getEntityCount();
    }

    public function getNextEntity()
    {
        $data = $this->parent->getNextEntity();
        return $this->processData($data);
    }

    public function putEntity($data)
    {
        $data = $this->processData($data);
        return $this->parent->putEntity($data);
    }

    public function rewind()
    {
        return $this->parent->rewind();
    }

    abstract protected function processData();

    public function finalize()
    {
        return $this->parent->rewind();
    }

}
