<?php
namespace Webbhuset\Bifrost\Core\Utils\Processor\Filler\Backend;
use Webbhuset\Bifrost\Core\Utils\Processor\ProcessorInterface;

class DefaultValues implements ProcessorInterface, \ArrayAccess
{
    protected $defaultValues;

    public function __construct($params)
    {
        if (!isset($params['default_values'])) {
            throw new BifrostException("'default_values' parameter is not set.");
        }
        $this->defaultValues = $params['default_values'];
    }

    public function getData() {
        return $this;
    }

    public function offsetExists($offset)
    {
        return true;
    }

    public function offsetGet($offset)
    {
        return $this->defaultValues;
    }

    public function offsetSet($offset, $value)
    {
    }

    public function offsetUnset($offset)
    {
    }

    public function init($args)
    {
    }

    public function processNext($data, $onlyForCount)
    {
    }

    public function finalize($onlyForCount)
    {
    }

    public function count()
    {
    }

    public function getNextSteps()
    {
    }
}
