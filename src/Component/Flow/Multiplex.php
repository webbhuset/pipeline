<?php

namespace Webbhuset\Bifrost\Core\Component\Flow;

use Webbhuset\Bifrost\Core\BifrostException;
use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Webbhuset\Bifrost\Core\Data\ActionData\ActionDataInterface;
use Webbhuset\Bifrost\Core\Helper\ReflectionHelper;

class Multiplex implements ComponentInterface
{
    protected $conditionCallback;
    protected $components;

    /**
     * Construct.
     *
     * @param callable $conditionCallback
     * @param array $components
     *
     * @return void
     */
    public function __construct(callable $conditionCallback, array $components)
    {
        $this->validateCallback($conditionCallback);

        foreach ($components as $key => $component) {
            if ($component === false) {
                unset($components[$key]);
                continue;
            }

            if (!is_object($component)) {
                throw new BifrostException("Component is not an object.");
            }

            if (!$component instanceof ComponentInterface) {
                $class = get_class($ccomponent);
                throw new BifrostException("Component {$class} does not implement 'ComponentInterface'");
            }
        }

        $this->conditionCallback    = $conditionCallback;
        $this->components           = $components;
    }

    public function process($items, $finalize = true)
    {
        foreach ($items as $item) {
            if ($item instanceof ActionDataInterface) {
                yield $item;
                continue;
            }

            $key = call_user_func($this->conditionCallback, $item);

            if (isset($this->components[$key])) {
                $results = $this->components[$key]->process([$item], false);
                foreach ($results as $result) {
                    yield $result;
                }
            }
        }

        if ($finalize) {
            foreach ($this->components as $component) {
                $results = $component->process([], true);
                foreach ($results as $result) {
                    yield $result;
                }
            }
        }
    }

    protected function validateCallback($callback)
    {
        $reflection = ReflectionHelper::getReflectionFromCallback($callback);

        if (!$reflection) {
            throw new BifrostException('Could not create reflection from callback parameter.');
        }

        $params = $reflection->getParameters();

        if (count($params) < 1) {
            throw new BifrostException('The callback requires 1 param, e.g. "function($item)".');
        }
        if (count($params) > 1) {
            foreach ($params as $idx => $param) {
                if ($idx == 0) {
                    continue;
                }
                if (!$param->isOptional()) {
                    $idx += 1;
                    throw new BifrostException("Callback function param {$idx} is not optional. All params except first has to be optional.");
                }
            }
        }
    }
}
