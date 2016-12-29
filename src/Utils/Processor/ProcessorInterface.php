<?php
namespace Webbhuset\Bifrost\Core\Utils\Processor;

interface ProcessorInterface
{
    public function init($args);
    public function processNext($data, $onlyForCount);
    public function finalize($onlyForCount);
    public function count();
}
