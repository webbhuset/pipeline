<?php
namespace Webbhuset\Bifrost\Core\Utils\Processor;

class Mock extends AbstractProcessor
{
    protected $count = 0;
    public function __construct($log = null, $nextChain = null, $params = null)
    {
    }

    public function processNext($data, $onlyForCount = false)
    {
        $this->count += count($data);
    }

    protected function processData($data)
    {
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
}
