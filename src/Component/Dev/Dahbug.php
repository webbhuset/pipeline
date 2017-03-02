<?php

namespace Webbhuset\Bifrost\Core\Component\Dev;

use Webbhuset\Bifrost\Core\Component\ComponentInterface;
use Webbhuset\Bifrost\Core\Data\ActionData\ActionDataInterface;

class Dahbug implements ComponentInterface
{
    protected $label;
    protected $log = true;

    public function __construct($label = 'dump', $log = true)
    {
        $this->label = $label;
        $this->log = $log;
    }

    public function process($items)
    {
        foreach ($items as $item) {
            if ($item instanceof ActionDataInterface) {
                yield $item;
                continue;
            }
            if ($this->log) {
                \dahbug::dump($item, $this->label, 10);
            }
            yield $item;
        }
    }
}
