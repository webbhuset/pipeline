<?php

namespace Webbhuset\Bifrost\Core\Component\Flow;

use Webbhuset\Bifrost\Core\BifrostException;
use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Webbhuset\Bifrost\Core\Data\ActionData\ActionDataInterface;
use Webbhuset\Bifrost\Core\Helper\ReflectionHelper;

class CaseSwitch implements ComponentInterface
{
    protected $cases;

    /**
     * Construct.
     *
     * Example $cases format:
     *  [
     *      [
     *          Condition1Callback,
     *          Component1
     *      ],
     *      [
     *          ConditionCallback,
     *          Component2
     *      ],
     *  ]
     *
     * @param array $cases
     *
     * @return void
     */
    public function __construct(array $cases)
    {
        foreach ($cases as $idx => $case) {
            if ($case === false) {
                unset($cases[$idx]);
                continue;
            }

            if (count($case) !== 2) {
                throw new BifrostException('Each case must contain a condition callback and a component');
            }

            if (!is_callable($case[0])) {
                throw new BifrostException('Condition must be a callable.');
            }

            $this->validateCallback($case[0]);

            if (!is_object($case[1])) {
                throw new BifrostException("Component is not an object.");
            }

            if (!$case[1] instanceof ComponentInterface) {
                $class = get_class($case[1]);
                throw new BifrostException("Component {$class} does not implement 'ComponentInterface'");
            }
        }

        $this->cases = $cases;
    }

    public function process($items, $finalize = true)
    {
        foreach ($items as $item) {
            if ($item instanceof ActionDataInterface) {
                yield $item;
                continue;
            }

            foreach ($this->cases as $case) {
                $condition = $case[0];
                $component = $case[1];

                if (!$condition($item)) {
                    continue;
                }

                $results = $component->process([$item], false);
                foreach ($results as $result) {
                    yield $result;
                }
                continue 2;
            }
        }

        if ($finalize) {
            foreach ($this->cases as $case) {
                $component  = $case[1];
                $results    = $component->process([], true);
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
