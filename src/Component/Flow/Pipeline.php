<?php

namespace Webbhuset\Bifrost\Component\Flow;

use Webbhuset\Bifrost\BifrostException;
use Webbhuset\Bifrost\Component\ComponentInterface;
use Webbhuset\Bifrost\Helper\ArrayHelper\Tree;

class Pipeline implements ComponentInterface
{
    protected $components;

    public function __construct(array $components)
    {
        $components = Tree::getLeaves($components);

        foreach ($components as $idx => $component) {
            if ($component === false) {
                unset($components[$idx]);
                continue;
            }
            if (!is_object($component)) {
                throw new BifrostException("Component is not an object.");
            }
            if (!$component instanceof ComponentInterface) {
                $class = get_class($component);
                throw new BifrostException("Component {$class} does not implement 'ComponentInterface'");
            }
        }
        $this->components = $components;
    }

    public function process($items, $finalize = true)
    {
        foreach ($this->components as $component) {
            $items = $component->process($items, $finalize);
        }

        return $items;
    }
}
