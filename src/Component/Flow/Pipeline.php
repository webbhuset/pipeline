<?php

namespace Webbhuset\Bifrost\Core\Component\Flow;

use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Webbhuset\Bifrost\Core\BifrostException;

class Pipeline implements ComponentInterface
{
    protected $processors;

    public function __construct(array $processors)
    {
        foreach ($processors as $idx => $processor) {
            if (!is_object($processor)) {
                throw new BifrostException("Component is not an object.");
            }
            if (!$processor instanceof ComponentInterface) {
                $class = get_class($processor);
                throw new BifrostException("Component {$class} does not implement 'ComponentInterface'");
            }
        }
        $this->processors = $processors;
    }

    public function process($items, $finalize = true)
    {
        foreach ($this->processors as $processor) {
            $items = $processor->process($items, $finalize);
        }

        return $items;
    }
}
