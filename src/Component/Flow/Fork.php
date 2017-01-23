<?php

namespace Webbhuset\Bifrost\Core\Component\Flow;

use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Webbhuset\Bifrost\Core\BifrostException;
use Webbhuset\Bifrost\Core\Monad\Action;

class Fork implements ComponentInterface
{
    protected $forks;

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
        $this->forks = $processors;
    }

    public function process($items, $finalize = true)
    {
        foreach ($items as $key => $item) {
            if (is_string($key)) {
                yield $key => $item;
                continue;
            }
            foreach ($this->forks as $fork) {
                $results = $fork->process([$item], false);
                foreach ($results as $key => $res) {
                    if (is_string($key)) {
                        yield $key => $res;
                        continue;
                    }
                    yield $res;
                }
            }
        }

        if ($finalize) {
            foreach ($this->forks as $fork) {
                $results = $fork->process([], true);
                foreach ($results as $key => $res) {
                    if (is_string($key)) {
                        yield $key => $res;
                        continue;
                    }
                    yield $res;
                }
            }
        }
    }
}
