<?php
namespace Webbhuset\Bifrost\Core\Utils\Writer\Mock;
use Webbhuset\Bifrost\Core\Utils\Writer\AbstractWriter;
use \Webbhuset\Bifrost\Core\Utils\Logger\LoggerInterface;

class Collector extends AbstractWriter
{
    protected $id;
    protected $data = [];
    protected $count = 0;

    public function __construct(LoggerInterface $log = null, $params = null)
    {
        if (isset($params['id'])) {
            $this->id = $params['id'];
        }
    }

    public function processNext($data, $onlyForCount = false)
    {
        $this->data  =  $data;
        $this->count += count($data);
    }

    public function finalize($onlyForCount = false)
    {
        $this->count = 0;
    }

    public function init($args)
    {
    }

    public function count()
    {
        return $this->count;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getId()
    {
        return $this->id;
    }
}
