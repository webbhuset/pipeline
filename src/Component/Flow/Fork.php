<?php

namespace Webbhuset\Bifrost\Component\Flow;

use Webbhuset\Bifrost\BifrostException;
use Webbhuset\Bifrost\Component\ComponentInterface;
use Webbhuset\Bifrost\Data\ActionData\ActionDataInterface;

class Fork implements ComponentInterface
{
    protected $forks;

    public function __construct(array $processors)
    {
        foreach ($processors as $idx => $processor) {
            if ($processor === false) {
                unset($processors[$idx]);
                continue;
            }
            if (!is_object($processor)) {
                throw new BifrostException("Component is not an object.");
            }
            if (!$processor instanceof ComponentInterface) {
                $class = get_class($processor);
                throw new BifrostException("Component {$class} does not implement 'ComponentInterface'");
            }
        }
        $this->forks = $processors;
    }

    public function process($items, $finalize = true)
    {
        foreach ($items as $item) {
            if ($item instanceof ActionDataInterface) {
                yield $item;
                continue;
            }

            foreach ($this->forks as $fork) {
                $results = $fork->process([$item], false);
                foreach ($results as $res) {
                    yield $res;
                }
            }
        }

        if ($finalize) {
            foreach ($this->forks as $fork) {
                $results = $fork->process([], true);
                foreach ($results as $res) {
                    yield $res;
                }
            }
        }
    }
}
